<?php

/// Interface to the Zanox API.
class Dfr_ZanoxAPIClient
{
    public $base_url = "http://api.zanox.com/xml/2011-03-01";

    function __construct($connect_id, $secret_key) {
        $this->_connect_id = $connect_id;
        $this->_secret_key = $secret_key;
        $this->_error = null;
    }

    function _prepare($verb, $params, $signed) {
        $qry = array();
        if(is_array($params))
            foreach($params as $k => $v)
                $qry[$k] = $v;
        $headers = null;
        if($signed) {
            $nonce = md5(microtime());
            $date = gmdate("D, d M Y H:i:s T");
            $sign = "GET/$verb/$date$nonce";
            $sign = hash_hmac("sha1", $sign, $this->_secret_key, true);
            $sign = base64_encode($sign);
            $headers = array(
                "Authorization: ZXWS {$this->_connect_id}:$sign",
                "Nonce: $nonce",
                "Date: $date"
            );
        } else {
            $qry["connectid"] = $this->_connect_id;
        }

        $url = "{$this->base_url}/$verb/";
        if($qry) {
            foreach($qry as $k => $v)
                $qry[$k] = rawurldecode($k) . "=" . rawurldecode($v);
            $url .= "?" . implode("&", $qry);
        }
        return array($url, $headers);
    }

    function _request($verb, $params=null, $signed=false) {
        list($url, $headers) = $this->_prepare($verb, $params, $signed);
        $c = curl_init($url);
        if($headers)
            curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($c);
        $err = curl_error($c);
        $errno = curl_errno($c);
        curl_close($c);
        if($err)
            return $this->_seterr("connection error: $errno $err");
        if(!strlen($xml))
            return $this->_seterr("connection error: empty response");
        $xml = simplexml_load_string($xml);
        if($xml->getName() == "error")
            return $this->_seterr("api error: " . strval($xml->message));
        return $xml;
    }

    function _seterr($err) {
        $this->_error = $err;
        return null;
    }

    /// Get the last error message.
    function error() {
        return $this->_error;
    }

    /// Return the list of adspaces for this account.
    function adspaces() {
        $xml = $this->_request("adspaces", array("items" => 50), true);
        if(!$xml)
            return array();
        $ls = array();
        foreach($xml->adspaceItems->adspaceItem as $e) {
            $p = json_decode(json_encode($e), true);
            $p["id"] = intval($p["@attributes"]["id"]);
            $p["regions"] = current($p["regions"]);
            $p["categories"] = current($p["categories"]);
            unset($p["@attributes"]);
            $ls[] = $p;
        }
        return $ls;
    }

    /// Return the list of programs for the given adspace id.
    /// If $adspace_id is 0, return all programs.
    function programs($adspace_id=0) {
        $ls = array();
        $total = null;
        for($page = 0; $page < 9999; $page++) {
            $params = array("items" => 50, "page" => $page);
            if($adspace_id)
                $params["adspace"] = $adspace_id;
            $xml = $this->_request("programapplications", $params, true);
            if(!$xml)
                return array();
            if(!isset($xml->programApplicationItems->programApplicationItem)) {
                $this->_seterr("api error: no program application items");
                return array();
            }
            foreach($xml->programApplicationItems->programApplicationItem as $e) {
                $pid = intval($e->program["id"]);
                $aid = intval($e->adspace["id"]);
                $p = json_decode(json_encode($e), true);
                $p["id"] = $pid;
                $p["adspace_id"] = $aid;
                $p["active"] = $e->program["active"] == "true";
                unset($p["@attributes"]);
                $ls[] = $p;
            }
            if(is_null($total))
                $total = intval($xml->total);
            $total -= intval($xml->items);
            if($total <= 0)
                break;
        }
        return $ls;
    }

    /// Return the list of zmids for the given adspace as an array of arrays
    /// (program_id, adspace_id, zmid)
    function zmids($adspace_id) {
        $ps = $this->programs($adspace_id);
        if(!$ps)
            return array();
        $ls = array();
        foreach($ps as $p) {
            if($p["status"] != "confirmed")
                continue;
            $xml = $this->_request("admedia", array("program" => $p["id"], "purpose" => "productdeeplink"));
            if(!$xml) {
                $this->_error = null;
                continue;
            }
            if(intval($xml->items)) {
                foreach($xml->admediumItems->admediumItem->trackingLinks->trackingLink as $tt) {
                    $ppv = strval($tt->ppv);
                    preg_match('~\?(\w+)~', $ppv, $m);
                    $ls[] = array($p["id"], intval($tt["adspaceId"]), $m[1]);
                }
            }
        }
        return $ls;
    }

    /// Return a single zmid for the specific (adspace, program) combination.
    function zmid($adspace_id, $program_id) {
        $xml = $this->_request("admedia", array("program" => $program_id, "purpose" => "productdeeplink"));
        if(!$xml)
            return null;
        if(!intval($xml->items))
            return null;
        foreach($xml->admediumItems->admediumItem->trackingLinks->trackingLink as $tt) {
            $ppv = strval($tt->ppv);
            preg_match('~\?(\w+)~', $ppv, $m);
            $aid = intval($tt["adspaceId"]);
            if($aid == $adspace_id)
                return $m[1];
        }
        return null;
    }


}
?>