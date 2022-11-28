<?php

class Dfrapi_SearchForm
{
    function fields() {

	    $opFulltext = array(
		    'match'       => __( 'matches', 'datafeedr-api' ),
		    'contain'     => __( 'contains', 'datafeedr-api' ),
		    'not_contain' => __( 'doesn\'t contain', 'datafeedr-api' ),
		    'start'       => __( 'starts with', 'datafeedr-api' ),
		    'end'         => __( 'ends with', 'datafeedr-api' ),
	    );

	    $opFulltextExact = array_merge( $opFulltext, array(
		    'is' => __( 'is', 'datafeedr-api' )
	    ) );

	    $opRange = array(
		    'eq'      => __( 'equal to', 'datafeedr-api' ),
		    'lt'      => __( 'less than', 'datafeedr-api' ),
		    'lte'     => __( 'less than or equal to', 'datafeedr-api' ),
		    'gt'      => __( 'greater than', 'datafeedr-api' ),
		    'gte'     => __( 'greater than or equal to', 'datafeedr-api' ),
		    'between' => __( 'between', 'datafeedr-api' )
	    );

	    $opIs = array(
		    'is' => __( 'is', 'datafeedr-api' )
	    );

	    $opIsIsnt = array(
		    'is'     => __( 'is', 'datafeedr-api' ),
		    'is_not' => __( 'isn\'t', 'datafeedr-api' )
	    );

	    $opYesNo = array(
		    'yes' => __( 'yes', 'datafeedr-api' ),
		    'no'  => __( 'no', 'datafeedr-api' )
	    );

	    $opInStock = array(
		    'yes_unknown' => __( 'yes or unknown', 'datafeedr-api' ),
		    'yes'         => __( 'yes', 'datafeedr-api' ),
		    'no'          => __( 'no', 'datafeedr-api' ),
	    );

	    $sortOpts = array(
		    ''              => __( 'Relevance', 'datafeedr-api' ),
		    '+price'        => __( 'Price Ascending', 'datafeedr-api' ),
		    '-price'        => __( 'Price Descending', 'datafeedr-api' ),
		    '+saleprice'    => __( 'Sale Price Ascending', 'datafeedr-api' ),
		    '-saleprice'    => __( 'Sale Price Descending', 'datafeedr-api' ),
		    '+finalprice'   => __( 'Final Price Ascending', 'datafeedr-api' ),
		    '-finalprice'   => __( 'Final Price Descending', 'datafeedr-api' ),
		    '+salediscount' => __( 'Discount Ascending', 'datafeedr-api' ),
		    '-salediscount' => __( 'Discount Descending', 'datafeedr-api' ),
		    '+merchant'     => __( 'Merchant', 'datafeedr-api' ),
		    '+time_created' => __( 'Date Created Ascending', 'datafeedr-api' ),
		    '-time_created' => __( 'Date Created Descending', 'datafeedr-api' ),
		    '+time_updated' => __( 'Last Updated Ascending', 'datafeedr-api' ),
		    '-time_updated' => __( 'Last Updated Descending', 'datafeedr-api' ),
		    '+_id'          => __( 'Product ID Ascending', 'datafeedr-api' ),
		    '-_id'          => __( 'Product ID Descending', 'datafeedr-api' ),
	    );

	    return array(
		    array(
			    'title'    => __( 'Any Field', 'datafeedr-api' ),
			    'name'     => 'any',
			    'input'    => 'text',
			    'operator' => $opFulltext,
			    'help'     => $this->help( 'any' )
		    ),
		    array(
			    'title'    => __( 'Product Name', 'datafeedr-api' ),
			    'name'     => 'name',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'name' )
		    ),
		    array(
			    'title'    => __( 'Product ID', 'datafeedr-api' ),
			    'name'     => 'id',
			    'input'    => 'text',
			    'operator' => $opIsIsnt,
			    'help'     => $this->help( 'id' )
		    ),
		    array(
			    'title'    => __( 'Barcode', 'datafeedr-api' ),
			    'name'     => 'barcode',
			    'input'    => 'text',
			    'operator' => $opIsIsnt,
			    'help'     => $this->help( 'barcode' )
		    ),
		    array(
			    'title'    => __( 'Brand', 'datafeedr-api' ),
			    'name'     => 'brand',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'brand' )
		    ),
		    array(
			    'title'    => __( 'Color', 'datafeedr-api' ),
			    'name'     => 'color',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'color' )
		    ),
		    array(
			    'title'    => __( 'Material', 'datafeedr-api' ),
			    'name'     => 'material',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'material' )
		    ),
		    array(
			    'title'    => __( 'Size', 'datafeedr-api' ),
			    'name'     => 'size',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'size' )
		    ),
		    array(
			    'title'    => __( 'Gender', 'datafeedr-api' ),
			    'name'     => 'gender',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'gender' )
		    ),
		    array(
			    'title'    => __( 'SKU', 'datafeedr-api' ),
			    'name'     => 'sku',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'sku' )
		    ),
		    array(
			    'title'    => __( 'Condition', 'datafeedr-api' ),
			    'name'     => 'condition',
			    'input'    => 'text',
			    'operator' => $opFulltextExact,
			    'help'     => $this->help( 'condition' )
		    ),
		    array(
			    'title'    => __( 'Description', 'datafeedr-api' ),
			    'name'     => 'description',
			    'input'    => 'text',
			    'operator' => $opFulltext,
			    'help'     => $this->help( 'description' )
		    ),
		    array(
			    'title'    => __( 'Tags', 'datafeedr-api' ),
			    'name'     => 'tags',
			    'input'    => 'text',
			    'operator' => array(
				    'in'     => __( 'contain', 'datafeedr-api' ),
				    'not_in' => __( 'don\'t contain', 'datafeedr-api' )
			    ),
			    'help'     => $this->help( 'tags' )
		    ),
		    array(
			    'title'    => __( 'Category', 'datafeedr-api' ),
			    'name'     => 'category',
			    'input'    => 'text',
			    'operator' => $opFulltext,
			    'help'     => $this->help( 'category' )
		    ),
		    array(
			    'title'    => __( 'Product Type', 'datafeedr-api' ),
			    'name'     => 'type',
			    'input'    => 'select',
			    'options'  => array(
				    'products' => __( 'Product', 'datafeedr-api' ),
				    'coupons'  => __( 'Coupon', 'datafeedr-api' )
			    ),
			    'operator' => $opIs,
			    'help'     => $this->help( 'type' )
		    ),
		    array(
			    'title'    => __( 'Currency', 'datafeedr-api' ),
			    'name'     => 'currency',
			    'input'    => 'select',
			    'options'  => array(
				    'AUD' => 'AUD',
				    'BRL' => 'BRL',
				    'CAD' => 'CAD',
				    'CHF' => 'CHF',
				    'CZK' => 'CZK',
				    'DKK' => 'DKK',
				    'EUR' => 'EUR',
				    'GBP' => 'GBP',
				    'INR' => 'INR',
				    'NOK' => 'NOK',
				    'NZD' => 'NZD',
				    'PLN' => 'PLN',
				    'SEK' => 'SEK',
				    'TRY' => 'TRY',
				    'USD' => 'USD',
			    ),
			    'operator' => $opIsIsnt,
			    'help'     => $this->help( 'currency' )
		    ),
		    array(
			    'title'    => __( 'Price', 'datafeedr-api' ),
			    'name'     => 'price',
			    'input'    => 'range',
			    'operator' => $opRange,
			    'help'     => $this->help( 'price' )
		    ),
		    array(
			    'title'    => __( 'Sale Price', 'datafeedr-api' ),
			    'name'     => 'saleprice',
			    'input'    => 'range',
			    'operator' => $opRange,
			    'help'     => $this->help( 'saleprice' )
		    ),
		    array(
			    'title'    => __( 'Final Price', 'datafeedr-api' ),
			    'name'     => 'finalprice',
			    'input'    => 'range',
			    'operator' => $opRange,
			    'help'     => $this->help( 'finalprice' )
		    ),
		    array(
			    'title'    => __( 'Discount', 'datafeedr-api' ),
			    'name'     => 'salediscount',
			    'input'    => 'range',
			    'operator' => $opRange,
			    'help'     => $this->help( 'salediscount' )
		    ),
		    array(
			    'title'    => __( 'Network', 'datafeedr-api' ),
			    'name'     => 'source_id',
			    'input'    => 'network',
			    'operator' => $opIsIsnt,
			    'help'     => $this->help( 'source_id' )
		    ),
		    array(
			    'title'    => __( 'Merchant', 'datafeedr-api' ),
			    'name'     => 'merchant_id',
			    'input'    => 'merchant',
			    'operator' => $opIsIsnt,
			    'help'     => $this->help( 'merchant_id' )
		    ),
		    array(
			    'title'    => __( 'On Sale', 'datafeedr-api' ),
			    'name'     => 'onsale',
			    'input'    => 'none',
			    'operator' => $opYesNo,
			    'help'     => $this->help( 'onsale' )
		    ),
		    array(
			    'title'    => __( 'In Stock', 'datafeedr-api' ),
			    'name'     => 'instock',
			    'input'    => 'none',
			    'operator' => $opInStock,
			    'help'     => $this->help( 'instock' )
		    ),
		    array(
			    'title'    => __( 'Has Direct URL', 'datafeedr-api' ),
			    'name'     => 'direct_url',
			    'input'    => 'none',
			    'operator' => $opYesNo,
			    'help'     => $this->help( 'direct_url' )
		    ),
		    array(
			    'title'    => __( 'Has Image', 'datafeedr-api' ),
			    'name'     => 'image',
			    'input'    => 'none',
			    'operator' => $opYesNo,
			    'help'     => $this->help( 'image' )
		    ),
		    array(
			    'title'    => __( 'Has Barcode', 'datafeedr-api' ),
			    'name'     => 'has_barcode',
			    'input'    => 'none',
			    'operator' => $opYesNo,
			    'help'     => $this->help( 'has_barcode' )
		    ),
		    array(
			    'title'    => __( 'Last Updated', 'datafeedr-api' ),
			    'name'     => 'time_updated',
			    'input'    => 'range',
			    'operator' => array( 'lt' => 'before', 'gt' => 'after', 'between' => 'between' ),
			    'help'     => $this->help( 'time_updated' )
		    ),
		    array(
			    'title'    => __( 'Limit', 'datafeedr-api' ),
			    'name'     => 'limit',
			    'input'    => 'text',
			    'operator' => array( 'is' => 'is' ),
			    'help'     => $this->help( 'limit' )
		    ),
		    array(
			    'title'    => __( 'Merchant Limit', 'datafeedr-api' ),
			    'name'     => 'merchant_limit',
			    'input'    => 'select',
			    'options'  => array_filter( range( 0, 50 ), function ( $num ) {
				    return $num != 0;
			    } ),
			    'operator' => array( 'is' => 'is' ),
			    'help'     => $this->help( 'merchant_limit' )
		    ),
		    array(
			    'title'    => __( 'Sort By', 'datafeedr-api' ),
			    'name'     => 'sort',
			    'input'    => 'none',
			    'operator' => $sortOpts,
			    'help'     => $this->help( 'sort' )
		    ),
		    array(
			    'title'    => __( 'Exclude Duplicates', 'datafeedr-api' ),
			    'name'     => 'duplicates',
			    'input'    => 'text',
			    'operator' => array(
				    'is' => __( 'matching these fields', 'datafeedr-api' ),
			    ),
			    'help'     => $this->help( 'duplicates' )
		    )
	    );
    }

	function defaults() {
		return array(
			'any'            => array( 'operator' => 'match', 'value' => '' ),
			'id'             => array( 'operator' => 'is', 'value' => '' ),
			'barcode'        => array( 'operator' => 'is', 'value' => '' ),
			'name'           => array( 'operator' => 'match', 'value' => '' ),
			'brand'          => array( 'operator' => 'match', 'value' => '' ),
			'color'          => array( 'operator' => 'match', 'value' => '' ),
			'material'       => array( 'operator' => 'match', 'value' => '' ),
			'size'           => array( 'operator' => 'match', 'value' => '' ),
			'gender'         => array( 'operator' => 'match', 'value' => '' ),
			'sku'            => array( 'operator' => 'match', 'value' => '' ),
			'condition'      => array( 'operator' => 'match', 'value' => '' ),
			'type'           => array( 'value' => 'product' ),
			'currency'       => array( 'value' => 'USD' ),
			'price'          => array( 'operator' => 'between', 'value' => '0', 'value2' => '999999' ),
			'saleprice'      => array( 'operator' => 'between', 'value' => '0', 'value2' => '999999' ),
			'source_id'      => array( 'value' => array() ),
			'merchant_id'    => array( 'value' => array() ),
			'onsale'         => array( 'value' => '1' ),
			'instock'        => array( 'value' => 'yes_unknown' ),
			'direct_url'     => array( 'value' => '1' ),
			'image'          => array( 'value' => '1' ),
			'has_barcode'    => array( 'value' => '1' ),
			'thumbnail'      => array( 'value' => '1' ),
			'time_updated'   => array( 'operator' => 'lt', 'value' => 'today' ),
			'limit'          => array( 'value' => 1000 ),
			'merchant_limit' => array( 'value' => 5 ),
		);
	}

    public $useSelected;
    public $prefix;

    function get($ary, $key, $default="") {
        return is_array($ary) && isset($ary[$key]) ? $ary[$key] : $default;
    }

    function inputPrefix($index) {
        return sprintf('%s[%s]', $this->prefix ? $this->prefix : "query", $index);
    }

    function byName($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    }

    function ary($s) {
        if(!is_array($s))
            $s = explode(',', trim($s));
        return array_filter($s, 'strlen');
    }

    function ids($lst) {
        $ids = array();
        foreach($lst as $obj)
            $ids []= $obj['_id'];
        return $ids;
    }

    function selectOpts($opts, $selectedValue=NULL) {
        $html = "";
        foreach($opts as $value => $title) {
            $sel = $selectedValue == $value ? "selected='selected'" : "";
            $title = htmlspecialchars($title);
            $value = htmlspecialchars($value);
            $html .= "<option value=\"$value\" $sel>$title</option>";
        }
        return $html;
    }

    function allNetworks() {
        $lst = dfrapi_api_get_all_networks();
        usort($lst, array($this, 'byName'));
        return $lst;
    }

    function selectedNetworks() {
        $selected = $this->get(get_option( 'dfrapi_networks' ), 'ids');
        if(empty($selected))
            return array();

        $lst = array();
        foreach($this->allNetworks() as $net)
            if(isset($selected[$net['_id']]))
                $lst []= $net;
        return $lst;
    }

    function groupClass($nid) {
        if (empty($nid)) { return ''; }
        $networks = $this->allNetworks();
        foreach($networks as $network) {
            if ($network['_id'] == $nid) {
                $name = str_replace( array( " ", "-", "." ), "", $network['group'] );
                $type = ( $network['type'] == 'coupons' ) ? '_coupons' : '';
                return 'network_logo_16x16_' . strtolower( $name . $type );
            }
        }
    }

    function selectedMerchants() {
        $selected = $this->get(get_option( 'dfrapi_merchants' ), 'ids');
        if(empty($selected))
            return array();

        $lst = array();
        foreach(dfrapi_api_get_merchants_by_id($selected) as $merchant) {
            $lst []= $merchant;
        }
        usort($lst, array($this, 'byName'));
        return $lst;
    }

    function networksMerchantsPopup($kind, $value) {
        $title = array(
            'network'  => __( 'Select Networks', 'datafeedr-api' ),
            'merchant' => __( 'Select Merchants', 'datafeedr-api' )
        );
        $clear = __( 'Clear search', 'datafeedr-api' );
        $ok = __( 'OK', 'datafeedr-api' );
        $cells = "";

        $all = ($kind == 'network') ?
            ($this->useSelected ? $this->selectedNetworks() : $this->allNetworks()) :
            $this->selectedMerchants();

        foreach($all as $obj) {
            $id = $obj['_id'];
            $nid = ($kind == 'network') ? $obj['_id'] : $obj['source_id'];
            $group_class = $this->groupClass($nid);

            $name = $obj['name'];
            $checked = in_array($id, $value) ? "checked='checked'" : "";

            $cells .= "
				<div class='inline_frame_element nid_{$nid}'>
					<label>
						<input type='checkbox' value='{$id}' $checked />
						<span class='element_name {$group_class}'>{$name}</span>
					</label>
				</div>
			";
        }

        return "
            <h1>{$title[$kind]}</h1>
            <div class='filter_action'>
                Search: <input type='text' />
                <a class='reset_search button' title='{$clear}'>&times;</a>
            </div>
            <div class='inline_frame'>
				<div>
					{$cells}
					<div class='clearfix'></div>
				</div>
			</div>
			<div><a class='button submit'>$ok</a></div>
		";
    }

    function networksMerchantsNames($kind, $value, $maxNames=5) {
        $all = ($kind == 'network') ? $this->allNetworks() : dfrapi_api_get_merchants_by_id($value);
        $names = array();
        foreach($all as $obj) {
            if(in_array($obj['_id'], $value)) {
                $names []= "<span>{$obj['name']}</span>";
                if(count($names) >= $maxNames)
                    break;
            }
        }
        $html = implode(', ', $names);
        if(count($value) - count($names)) {
            $html  .= ' ' . sprintf(__( 'and %s more', 'datafeedr-api' ), count($value) - count($names));
        }
        return $html;
    }

    function chooseBox($kind, $field, $index, $value) {
        $pfx = $this->inputPrefix($index);
        $value = implode(',', $this->ary($value));
        $choose = __( 'choose', 'datafeedr-api' );
        return "
            <div class='dfrapi_choose_box' rel='{$kind}'>
                <span class='names'></span>
                <a class='button choose_{$kind}'>{$choose}</a>
                <input name='{$pfx}[value]' type='hidden' value=\"$value\"/>
            </div>
        ";
    }

	function ajaxHandler() {
		$command           = $this->get( $_POST, 'command' );
		$value             = $this->ary( $this->get( $_POST, 'value' ) );
		$this->useSelected = (int) $this->get( $_POST, 'useSelected', 1 );

		if ( $command === 'choose_network' ) {
			return $this->networksMerchantsPopup( 'network', $value );
		}

		if ( $command === 'choose_merchant' ) {
			return $this->networksMerchantsPopup( 'merchant', $value );
		}

		if ( $command === 'names_network' ) {
			return $this->networksMerchantsNames( 'network', $value );
		}

		if ( $command === 'names_merchant' && ! empty( $value ) ) {
			return $this->networksMerchantsNames( 'merchant', $value );
		}

//	    switch ( $command ) {
//		    case "choose_network":
//			    return $this->networksMerchantsPopup( 'network', $value );
//		    case "choose_merchant":
//			    return $this->networksMerchantsPopup( 'merchant', $value );
//		    case "names_network":
//			    return $this->networksMerchantsNames( 'network', $value );
//		    case "names_merchant":
//			    return $this->networksMerchantsNames( 'merchant', $value );
//	    }

		return '';
	}

    function renderField($field, $index, $params) {
        $pfx = $this->inputPrefix($index);
        $value = $this->get($params, 'value');
        $operator = $this->get($params, 'operator');
        $input = "";
        $percent_sign = ( isset( $field['name'] ) && $field['name'] == 'salediscount') ? '%' : '';

        switch($field['input']) {
            case 'text':
                $value = htmlspecialchars($value);
                $input = "<input class='long' name='{$pfx}[value]' type='text' value=\"$value\"/>";
                break;
            case 'select':
                $opts  = $this->selectOpts($field['options'], $value);
                $input = "<select name='{$pfx}[value]'>{$opts}</select>";
                break;
            case 'range':
                $value  = htmlspecialchars($value);
                $value2 = htmlspecialchars($this->get($params, 'value2'));
                $and = __( 'and', 'datafeedr-api' );
                $input = "
        			<input class='short' name='{$pfx}[value]' type='text' value=\"$value\"/>{$percent_sign}
		        	<span class='value2' style='display:none'>
			            $and
			            <input class='short' name='{$pfx}[value2]' type='text' value=\"$value2\" />
                    </span>
		        ";
                break;
            case 'network':
                $input = $this->chooseBox('network', $field, $index, $value);
                break;
            case 'merchant':
                $input = $this->chooseBox('merchant', $field, $index, $value);
                break;
            case 'none':
                $input = "";
                break;
        }

        if(count($field['operator']) == 1) {
            $key = key($field['operator']);
            $val = current($field['operator']);
            $operator = "
                <input type='hidden' name='{$pfx}[operator]' value=\"{$key}\" />
                <span>{$val}</span>
            ";
        } else {
            $opts = $this->selectOpts($field['operator'], $operator);
            $operator = "<select name='{$pfx}[operator]'>{$opts}</select>";
        }
        return array($operator, $input);
    }

    function renderRow($field, $index, $params, $show) {
        $pfx = $this->inputPrefix($index);
        list($operator, $input) = $this->renderField($field, $index, $params);
        $fieldOpts = '';
        foreach($this->fields() as $f) {
            if(!$this->useSelected && $f['name'] == 'merchant_id')
                continue;
            $sel = $field['name'] == $f['name'] ? "selected='selected'" : "";
            $fieldOpts .= "<option value=\"{$f['name']}\" $sel>{$f['title']}</option>";
        }
        $style = $show ? "" : "style='display:none'";
        return "
            <div class='filter filter_{$field['name']}' {$style}>
                <div class='valuewrapper'>
                    <div class='value'>{$input}</div>
                </div>
                <div class='plusminus'><a class='minus'></a> </div>
                <div class='field'><select name='{$pfx}[field]' style='width: 140px'>{$fieldOpts}</select><a href='#' class='dfrapi_search_help'>?</a></div>
                <div class='operator'>{$operator}</div>
                <div class='clearfix'></div>
                <div class='help' style='display:none;'><a href='#' class='dfrapi_search_help'><span class='dashicons dashicons-no'> </span></a>{$field['help']}</div>
            </div>
        ";
    }

    function render($prefix, $query, $useSelected=true) {
        $this->prefix = $prefix;
        $this->useSelected = intval($useSelected);
        if(!$query)
            $query = array(
                array('field'=>'any', 'value'=>'')
            );
        $defaults = $this->defaults();

        $fieldMap = array();
        $fieldUsed = array();
        foreach($this->fields() as $f) {
            if(!$this->useSelected && $f['name'] == 'merchant_id')
                continue;
            $fieldMap[$f['name']] = $f;
        }

        $form = "";
        $index = 0;
        foreach($query as $params) {
            $field = $this->get($fieldMap, $this->get($params, 'field'));
            if(!$field)
                continue;
            $fieldUsed[$field['name']] = 1;
            $form .= $this->renderRow($field, $index++, $params, true);
        }
        $show = ($index == 0); // if none shown, show the first
        foreach($fieldMap as $field) {
            $name = $field['name'];
            if(!isset($fieldUsed[$name])) {
                $form .= $this->renderRow($field, $index++, isset($defaults[$name]) ? $defaults[$name] : null, $show);
                $show = FALSE;
            }
        }

        $loading = __( 'Loading, please wait', 'datafeedr-api' );
        $add = __( 'add filter', 'datafeedr-api' );
        return "
            <div id='dfrapi_search_form'>
                <input type='hidden' id='dfrapi_useSelected' value='{$this->useSelected}' />
                <div id='dfprs_loading_content' style='display:none'>
                    <div class='dfrapi_loading'></div>
                    <h3>{$loading}</h3>
                </div>
                {$form}
                <div class='clearfix'></div>
                <div id='dfrapi_search_form_filter'><a href='#'><span class='dashicons dashicons-plus'> </span> $add</a></div>
            </div>
        ";
    }

    function combineLists($lst, $func) {
        if(!count($lst))
            return array();
        if(count($lst) == 1)
            return current($lst);
        $lst = call_user_func_array($func, $lst);
        return count($lst) ? $lst : NULL;
    }

    function idFilter($inList, $exList) {
        $inList = $this->combineLists($inList, 'array_intersect');
        $exList = $this->combineLists($exList, 'array_merge');

        if(is_null($inList) || is_null($exList)) {
            return NULL;
        }
        if(count($inList)) {
            if(count($exList)) {
                $inList = array_diff($inList, $exList);
                if(!count($inList))
                    return NULL;
            }
            return array("IN", $inList);
        }
        if(count($exList)) {
            return array("!IN", $exList);
        }
        return array(NULL, NULL);
    }

    function fulltextFilter($operator, $value) {
        if($operator == 'match')
            return array("LIKE", $value);
        $value = trim(preg_replace('~[!\\[\\]"^$]+~', " ", $value));
        if(strlen($value)) {
            switch($operator) {
                case 'is':          return array('LIKE', "^$value\$");
                case 'contain':     return array('LIKE', $value);
                case 'not_contain': return array('!LIKE', $value);
                case 'start':       return array('LIKE', "^$value");
                case 'end':         return array('LIKE', "$value\$");
            }
        }
        return array(NULL, NULL);
    }

    function makeFilters($query, $useSelected=TRUE) {

        $filters   = array();

        $selected = array(
            'in:source_id'   => array(),
            'ex:source_id'   => array(),
            'in:merchant_id' => array(),
            'ex:merchant_id' => array(),
        );

        $allNetworks = $this->ids($this->allNetworks());

        if($useSelected) {
            $selected['in:source_id']   []= $this->ids($this->selectedNetworks());
            $selected['in:merchant_id'] []= $this->ids($this->selectedMerchants());
        } else {
            $selected['in:source_id']   []= $allNetworks;
        }

        foreach($query as $params) {
            $fname    = $params['field'];
            $value    = $this->get($params, 'value');
            $value2   = $this->get($params, 'value2');
            $operator = strtolower($this->get($params, 'operator'));

            switch($fname) {
                case 'any':
                case 'name':
                case 'brand':
                case 'color':
                case 'material':
                case 'size':
                case 'gender':
                case 'sku':
                case 'condition':
                case 'description':
                case 'merchant':
                case 'source':
                case 'category':
                    $s = $this->fulltextFilter($operator, $value);
                    if(!is_null($s[0])) {
                        $filters []= "{$fname} {$s[0]} {$s[1]}";
                    }
                    break;
                case 'tags':
                    $operator = ($operator == 'in') ? "LIKE" : "!LIKE";
                    $filters []= "{$fname} {$operator} {$value}";
                    break;
                case 'currency':
                    $op = ($operator == 'is') ? '=' : '!=';
                    $filters []= "{$fname} $op {$value}";
                    break;
	            case 'id':
	            case 'barcode':
		            $op = ($operator == 'is') ? 'IN' : '!IN';
		            $filters []= "{$fname} $op {$value}";
		            break;
                case 'price':
                case 'finalprice':
                case 'saleprice':
                case 'salediscount':
                    $conv = $fname == 'salediscount' ? 'intval' : 'dfrapi_price_to_int';
                    $value = $conv($value);
                    switch($operator) {
                        case 'between':
                            $value2 = $conv($value2);
                            $filters []= "{$fname} > {$value}";
                            $filters []= "{$fname} < {$value2}";
                            break;
                        case 'eq':
                            $filters []= "{$fname} = {$value}";
                            break;
                        case 'lt':
                            $filters []= "{$fname} < {$value}";
                            break;
                        case 'lte':
                            $filters []= "{$fname} <= {$value}";
                            break;
                        case 'gt':
                            $filters []= "{$fname} > {$value}";
                            break;
                        case 'gte':
                            $filters []= "{$fname} >= {$value}";
                            break;
                    }
                    break;
                case 'source_id':
                case 'merchant_id':
                    $key = ($operator == 'is') ? 'in' : 'ex';
                    $selected["$key:$fname"] []= $this->ary($value);
                    break;
                case 'type':
                    $ids = array();
                    foreach($this->allNetworks() as $net) {
                        if($net['type'] == $value)
                            $ids []= $net['_id'];
                    }
                    $selected["in:source_id"] []= $ids;
                    break;
                case 'onsale':
                    $value = ($operator == 'yes') ? '1' : '0';
                    $filters []= "{$fname} = {$value}";
                    break;
	            case 'instock':
		            if ( $operator == 'yes' ) {
			            $filters [] = "{$fname} = 1";
			            break;
		            } elseif ( $operator == 'no' ) {
			            $filters [] = "{$fname} = 0";
			            break;
		            } else {
			            $filters [] = "{$fname} > 0";
			            break;
		            }
	            case 'direct_url':
                    $operator = ($operator == 'yes') ? '!EMPTY' : 'EMPTY';
                    $filters []= "{$fname} {$operator}";
                    break;
                case 'image':
	            case 'thumbnail':
		            $operator = ($operator == 'yes') ? '!EMPTY' : 'EMPTY';
		            $filters []= "image {$operator}";
		            break;
	            case 'has_barcode':
		            $operator = ( $operator === 'yes') ? '!EMPTY' : 'EMPTY';
		            $filters []= "barcode {$operator}";
		            break;
                case 'time_updated':
                    $value = @date('Y-m-d H:i:s', strtotime($value));
                    switch($operator) {
                        case 'between':
                            $value2 = @date('Y-m-d H:i:s', strtotime($value2));
                            $filters []= "{$fname} > {$value}";
                            $filters []= "{$fname} < {$value2}";
                            break;
                        case 'lt':
                            $filters []= "{$fname} < {$value}";
                            break;
                        case 'gt':
                            $filters []= "{$fname} > {$value}";
                            break;
                    }
                    break;
            }
        }

        $s = $this->idFilter($selected['in:source_id'], $selected['ex:source_id']);
        if(is_null($s))
            return array('error' => 'No networks selected');
        if(!is_null($s[0]) && count($s[1]) < count($allNetworks))
            $filters []= "source_id {$s[0]} ". implode(',', $s[1]);

        $s = $this->idFilter($selected['in:merchant_id'], $selected['ex:merchant_id']);
        if(is_null($s))
            return array('error' => 'No merchants selected');
        if(!is_null($s[0])) {
            $m = implode(',', $s[1]);
            if ($m)
                $filters []= "merchant_id {$s[0]} $m";
        }

        return $filters;

    }

    function help_tip($tip) {
        return '
    	<div class="dfrapi_search_tip">
    		<span class="dashicons dashicons-lightbulb"></span>
    		<p>
    			<strong>' . __( 'TIP:', 'datafeedr-api' ) . '</strong> ' .
               $tip . '
    		</p>
    	</div>
    	';
    }

    function help_operators() {
        return '
    		<p>
    			<span class="dashicons dashicons-info"></span> 
    			<em>
    				<a href="https://datafeedrapi.helpscoutdocs.com/article/216-search-operators" target="_blank">' . __('Learn more', 'datafeedr-api' ) . '</a> ' .
               __('about advanced uses of this field.', 'datafeedr-api' ) . '
    			</em>
    		</p>
    	';
    }

    function help($field) {

        $help = array();

        // Any
        $help['any'] = '<h3>' . __('Any Field', 'datafeedr-api' ) . '</h3>';
        $help['any'] .= '<p>' . __( 'Search all indexed text fields at once to return a broad set of results.', 'datafeedr-api' ) . '</p>';
        $help['any'] .= '<p>' . __( 'The fields listed below are indexed as text fields and searchable using Any Field. Image, currency, and price fields are not searched.', 'datafeedr-api' ) . '</p>';
        $help['any'] .= '<p><em>' . __( '*Note that not all merchants and networks provide every field. ', 'datafeedr-api' ) . '</em></p>';
        $help['any'] .= '<p>' . __( 'Fields searched using Any Field:', 'datafeedr-api' ) . '</p>';
        $help['any'] .= '<table width="100%" border="0" style="margin-bottom:20px"><tr><td width="33%" valign="top">accommodationtype<br />address<br />artist<br />author<br />bestsellers<br />brand<br />category<br />city<br />color<br />commissiontype<br />condition<br />country<br />county<br />description<br />destination<br />discount</td><td width="33%" valign="top">discounttype<br />ean<br />fabric<br />featured<br />flavour<br />gender<br />genre<br />isbn<br />language<br />location<br />manufacturer<br />manufacturerid<br />material<br />size<br />gender<br />model<br />mpn<br />name</td><td width="33%" valign="top">offercode<br />offertype<br />platform<br />productnumber<br />promo<br />publisher<br />rating<br />region<br />size<br />sku<br />stars<br />state<br />subcategory<br />tags<br />upc<br />weight</td></tr></table>';
        $help['any'] .= $this->help_operators();

        // Name
        $help['name'] = '<h3>' . __('Product Name', 'datafeedr-api' ) . '</h3>';
        $help['name'] .= '<p>' . __( 'Search by product name to narrow your results.', 'datafeedr-api' ) . '</p>';
        $help['name'] .= $this->help_tip( __( 'Some merchants include color, size, gender, product codes, sale information and promotions in the product name field.', 'datafeedr-api' ) );
        $help['name'] .= $this->help_operators();

        // ID
        $help['id'] = '<h3>' . __('Product ID', 'datafeedr-api' ) . '</h3>';
        $help['id'] .= '<p>' . __( 'Search by Product ID to find specific products.', 'datafeedr-api' ) . '</p>';
        $help['id'] .= $this->help_tip( __( 'Separate each product ID with a comma "," to search for multiple products by their IDs.<br /><br />Example: <strong>4088900432286084, 8177000167015036, 2651801988581531</strong>', 'datafeedr-api' ) );
        $help['id'] .= '<p>' . __( 'Product IDs are assigned by Datafeedr and are not related to any IDs merchants use to identify their products. Each product in the Datafeedr product database has a unique Product ID.', 'datafeedr-api' ) . '</p>';
//        $help['id'] .= $this->help_operators();

	    // Barcode
	    $help['barcode'] = '<h3>' . __('Barcode', 'datafeedr-api' ) . '</h3>';
	    $help['barcode'] .= '<p>' . __( 'Search for products by their barcode (ie. EAN, UPC or GTIN value).', 'datafeedr-api' ) . '</p>';
	    $help['barcode'] .= $this->help_tip( __( 'Separate each barcode with a comma "," to search for multiple products by their barcodes.<br /><br />Example: <strong>889169871993, 889169900167, 889169488559</strong>', 'datafeedr-api' ) );
	    $help['barcode'] .= '<p>' . __( 'The Barcode search filter ignores leading zeros. Therefore, searching for <strong>889169871993</strong> and searching for <strong>00889169871993</strong> will return the same results.', 'datafeedr-api' ) . '</p>';

        // Brand
        $help['brand'] = '<h3>' . __('Brand', 'datafeedr-api' ) . '</h3>';
        $help['brand'] .= '<p>' . __( 'Search by brand name to get specific results. Not every item has a brand name.', 'datafeedr-api' ) . '</p>';
        $help['brand'] .= $this->help_tip( __( 'Enter the shorter version of a brand name in this field. Omit words such as "Incorporated", "Limited" and their abbreviations like Inc., Ltd. and so on.', 'datafeedr-api' ) );
        $help['brand'] .= $this->help_operators();

        // Color
        $help['color'] = '<h3>' . __('Color', 'datafeedr-api' ) . '</h3>';
        $help['color'] .= '<p>' . __( 'Search by color to get specific results. Not every item has a color field.', 'datafeedr-api' ) . '</p>';

	    // Material
	    $help['material'] = '<h3>' . __('Material', 'datafeedr-api' ) . '</h3>';
	    $help['material'] .= '<p>' . __( 'Search by material to get specific results. Not every item has a material field.', 'datafeedr-api' ) . '</p>';

	    // Size
	    $help['size'] = '<h3>' . __('Size', 'datafeedr-api' ) . '</h3>';
	    $help['size'] .= '<p>' . __( 'Search by size to get specific results. Not every item has a size field.', 'datafeedr-api' ) . '</p>';

	    // Gender
	    $help['gender'] = '<h3>' . __('Gender', 'datafeedr-api' ) . '</h3>';
	    $help['gender'] .= '<p>' . __( 'Search by gender to get specific results. Not every item has a gender field.', 'datafeedr-api' ) . '</p>';

	    // SKU
	    $help['sku'] = '<h3>' . __('SKU', 'datafeedr-api' ) . '</h3>';
	    $help['sku'] .= '<p>' . __( 'Search by SKU to get specific results. Not every item has a SKU field.', 'datafeedr-api' ) . '</p>';

	    // Condition
	    $help['condition'] = '<h3>' . __('Condition', 'datafeedr-api' ) . '</h3>';
	    $help['condition'] .= '<p>' . __( 'Search by condition to get specific results. Not every item has a condition field.', 'datafeedr-api' ) . '</p>';

        // Description
        $help['description'] = '<h3>' . __('Description', 'datafeedr-api' ) . '</h3>';
        $help['description'] .= '<p>' . __( 'Search the description field for product attributes such as size, color, material, gender or usage.', 'datafeedr-api' ) . '</p>';
        $help['description'] .= $this->help_tip( __( 'Most merchants provide a product description. Some product details may only appear in the description and not in any other field. However, some merchants duplicate the product name or product code in the description field without supplying additional details or leave the product description blank.', 'datafeedr-api' ) );
        $help['description'] .= $this->help_operators();

        // Tags
        $help['tags'] = '<h3>' . __('Tags', 'datafeedr-api' ) . '</h3>';
        $help['tags'] .= '<p>' . __( 'Limit search results based on product tag and keyword information.', 'datafeedr-api' ) . '</p>';
        $help['tags'] .= $this->help_tip( __( 'This field contains data from various keyword-related fields provided by the merchant. This field does not always exist.', 'datafeedr-api' ) );

        // Category
        $help['category'] = '<h3>' . __('Category', 'datafeedr-api' ) . '</h3>';
        $help['category'] .= '<p>' . __( 'Limit search results based on category information.', 'datafeedr-api' ) . '</p>';
        $help['category'] .= $this->help_tip( __( 'This field contains data from various category-related fields provided by the merchant. This field does not always exist.', 'datafeedr-api' ) );
        $help['category'] .= $this->help_operators();

        // Type
        $help['type'] = '<h3>' . __('Product Type', 'datafeedr-api' ) . '</h3>';
        $help['type'] .= '<p>' . __( 'Limit your search results to one type of item, either Product or Coupon.', 'datafeedr-api' ) . '</p>';
        $help['type'] .= $this->help_tip( __( 'In order to use this filter, you must have already selected merchants that provide that type of item. For example, if you choose "Product type: Coupon" but have not selected any merchants that offer coupons, your search will return an error.', 'datafeedr-api' ) );

        // Currency
        $help['currency'] = '<h3>' . __('Currency', 'datafeedr-api' ) . '</h3>';
        $help['currency'] .= '<p>' . __( 'Limit your search results to items with a specific currency code.', 'datafeedr-api' ) . '</p>';
        $help['currency'] .= $this->help_tip( __( 'Selecting a currency code is one way to limit your search results to items from a specific country. However, not every item has been given a currency code value by the merchant. Items without a currency code will be excluded from your search results.', 'datafeedr-api' ) );

        // Price
        $help['price'] = '<h3>' . __('Price', 'datafeedr-api' ) . '</h3>';
        $help['price'] .= '<p>' . __( 'Filter your search results based on price. Return products less than, greater than, or within a price range that you set.', 'datafeedr-api' ) . '</p>';
        $help['price'] .= $this->help_tip( __( 'This field does not search on sale price. If you set a price range to less than 30, you will exclude an item with a regular price of 40 that is on sale for less than 30.', 'datafeedr-api' ) );

        // Sale Price
        $help['saleprice'] = '<h3>' . __('Sale Price', 'datafeedr-api' ) . '</h3>';
        $help['saleprice'] .= '<p>' . __( 'Filter your search results based on sale price. Return items less than, greater than, or within a sale price range that you set.', 'datafeedr-api' ) . '</p>';
        $help['saleprice'] .= $this->help_tip( __( 'This field does not search on regular price.  If you set a sale price range to between 50 and 100, an item with a sale price of 40 will be excluded even if it\'s regular price matches the range you set.', 'datafeedr-api' ) );

        // Final Price
        $help['finalprice'] = '<h3>' . __('Final Price', 'datafeedr-api' ) . '</h3>';
        $help['finalprice'] .= '<p>' . __( 'Filter your search results based on the final price. Return items less than, greater than, or within a sale price range that you set.', 'datafeedr-api' ) . '</p>';
        $help['finalprice'] .= $this->help_tip( __( 'The final price is the lower price when comparing the regular price and sale price field.', 'datafeedr-api' ) );

	    // Discount
	    $help['salediscount'] = '<h3>' . __('Discount', 'datafeedr-api' ) . '</h3>';
	    $help['salediscount'] .= '<p>' . __( 'Limit your search results to items with a specified discount. Enter the number in terms of percentage (1 - 100) to indicate a discount less than, greater than, or between a given range. You do not need to enter the percentage sign.', 'datafeedr-api' ) . '</p>';
	    $help['salediscount'] .= $this->help_tip( __( 'To display, for example, only products that are on sale for a discount of 20% or more, choose the "greater than" operator and type "19".', 'datafeedr-api' ) );

        // Network
        $help['source_id'] = '<h3>' . __('Network', 'datafeedr-api' ) . '</h3>';
        $help['source_id'] .= '<p>' . __( 'Limit your search results to items from one or more affiliate networks.', 'datafeedr-api' ) . '</p>';
        $help['source_id'] .= $this->help_tip( __( 'Affiliate networks are generally country-specific. Using the Network filter is one way to limit your search results by country.', 'datafeedr-api' ) );

        // Merchant
        $help['merchant_id'] = '<h3>' . __('Merchant', 'datafeedr-api' ) . '</h3>';
        $help['merchant_id'] .= '<p>' . __( 'Limit your search results to items from one or more merchants.', 'datafeedr-api' ) . '</p>';
        $help['merchant_id'] .= $this->help_tip( __( 'Using the Merchant filter is a quick way to exclude unrelated products and reduce the number of API requests made when building a Product Set. For example, if you are creating a Product Set related to cat products, exclude all merchants that only sell dog products.', 'datafeedr-api' ) );

        // On Sale
        $help['onsale'] = '<h3>' . __('On Sale', 'datafeedr-api' ) . '</h3>';
        $help['onsale'] .= '<p>' . __( 'Set this field to "<strong>yes</strong>" to return only items which are on sale. To exclude products which are on sale, set this field to "<strong>no</strong>".', 'datafeedr-api' ) . '</p>';

        // In Stock
        $help['instock'] = '<h3>' . __('In Stock', 'datafeedr-api' ) . '</h3>';
        $help['instock'] .= '<p>' . __( 'This allows you to filter products by stock status.', 'datafeedr-api' ) . '</p>';
        $help['instock'] .= '<p>' . __( '<strong>yes or unknown</strong>: This will return products which are explicitly set as "in-stock" or products which have no field containing stock-related information.', 'datafeedr-api' ) . '</p>';
        $help['instock'] .= '<p>' . __( '<strong>yes</strong>: This will only return products which are explicitly set as "in-stock".', 'datafeedr-api' ) . '</p>';
        $help['instock'] .= '<p>' . __( '<strong>no</strong>: This will only return products which are explicitly set as NOT "in-stock".', 'datafeedr-api' ) . '</p>';
        $help['instock'] .= $this->help_tip( __( 'Not all products contain stock-related information. Therefore selecting "yes or unknown" is the recommended choice when you want to find products which are "in-stock".', 'datafeedr-api' ) );

        // Has Direct URL
        $help['direct_url'] = '<h3>' . __('Has Direct URL', 'datafeedr-api' ) . '</h3>';
        $help['direct_url'] .= '<p>' . __( 'Limit your search results to items which have a direct URL (or which don\'t).', 'datafeedr-api' ) . '</p>';
        $help['direct_url'] .= $this->help_tip( __( 'A "Direct URL" is a URL directly to the product page on the merchant\'s website. By default, the "Direct URL" is never used as the URL in your "Buy" links. However, if you need the products in your store to contain a "Direct URL" to the product page on the merchants\' websites, then you should use this filter. This is most useful if you are using Skimlinks (or similar service) to generate your affiliate links instead of the affiliate networks.', 'datafeedr-api' ) );

	    // Has Image
	    $help['image'] = '<h3>' . __('Has Image', 'datafeedr-api' ) . '</h3>';
	    $help['image'] .= '<p>' . __( 'Limit your search results to items which have an image (or which don\'t).', 'datafeedr-api' ) . '</p>';
	    $help['image'] .= $this->help_tip( __( 'Sometimes the image URL in the merchant\'s data feed is broken. Items with broken images will still return in search results even though there appears to be no image.', 'datafeedr-api' ) );

	    // Has Barcode
	    $help['has_barcode'] = '<h3>' . __('Has Barcode', 'datafeedr-api' ) . '</h3>';
	    $help['has_barcode'] .= '<p>' . __( 'Limit your search results to products which have a barcode (ie. a UPC, EAN or GTIN value).', 'datafeedr-api' ) . '</p>';

	    // Last Updated
        $help['time_updated'] = '<h3>' . __('Last Updated', 'datafeedr-api' ) . '</h3>';
        $help['time_updated'] .= '<p>' . __( 'Filter products by the last time they were updated by the merchant. Enter an English textual datetime description using PHP\'s <a href="http://www.php.net/strtotime" target="_blank">strtotime()</a> function.', 'datafeedr-api' ) . '</p>';
        $help['time_updated'] .= '<h3>' . __('Examples', 'datafeedr-api' ) . '</h3>';
        $help['time_updated'] .= '<p>';
        $help['time_updated'] .= '<tt>' . __('last week', 'datafeedr-api' ) . '</tt><br />';
        $help['time_updated'] .= '<tt>' . __('2 days ago', 'datafeedr-api' ) . '</tt><br />';
        $help['time_updated'] .= '<tt>' . __('1 month ago', 'datafeedr-api' ) . '</tt>';
        $help['time_updated'] .= '</p>';
        $help['time_updated'] .= '<p>' . __( '', 'datafeedr-api' ) . '</p>';
        $help['time_updated'] .= $this->help_tip( __( 'An item\'s Last Updated date will not change if the merchant does not change any product information during a data feed update.', 'datafeedr-api' ) );

        // Limit
        $help['limit'] = '<h3>' . __('Limit', 'datafeedr-api' ) . '</h3>';
        $help['limit'] .= '<p>' . __( 'Limit the number of items returned in your search results. The maximum number of products that can be returned is 10,000.', 'datafeedr-api' ) . '</p>';
        $help['limit'] .= $this->help_tip( __( 'Limiting the number of products returned helps reduce the number of API requests made during searching and updating Product Sets.', 'datafeedr-api' ) );

        // Merchant Limit
        $help['merchant_limit'] = '<h3>' . __('Merchant Limit', 'datafeedr-api' ) . '</h3>';
        $help['merchant_limit'] .= '<p>' . __( 'Limit the number of items returned per merchant. The number must be between 1 and 50.', 'datafeedr-api' ) . '</p>';
        $help['merchant_limit'] .= $this->help_tip( __( 'This is useful to use when you have one merchant which has many products that match your search but other merchants have fewer of those same products and you want equal distribution between all merchants.', 'datafeedr-api' ) );

        // Sort By
        $help['sort'] = '<h3>' . __('Sort By', 'datafeedr-api' ) . '</h3>';
        $help['sort'] .= '<p>' . __( 'Change the sort criteria by which items are displayed in your search results.', 'datafeedr-api' ) . '</p>';
        $help['sort'] .= $this->help_tip( __( 'By default, search results are sorted by relevance. You can change that to sort by price, sale price, discount, or last updated date in ascending order (lowest to highest) or descending order (highest to lowest). You can also sort by merchant, which lists items alphabetically by merchant name.', 'datafeedr-api' ) );

        // Exclude Duplicates
        $help['duplicates'] = '<h3>' . __('Exclude Duplicates', 'datafeedr-api' ) . '</h3>';
        $help['duplicates'] .= '<p>' . __( 'Exclude items that contain identical product names, image URLs, etc. Enter one or more terms from the list below. Separate terms by a space (meaning AND) or | (pipe symbol, meaning OR).', 'datafeedr-api' ) . '<br />';
        $help['duplicates'] .= '<p>' . __( 'Enter one of these terms to exclude duplicates matching these fields:', 'datafeedr-api' ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('name', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('brand', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('description', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('currency', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('price', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('saleprice', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('source_id', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('merchant_id', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('direct_url', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('onsale', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('image', 'datafeedr-api' ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('thumbnail', 'datafeedr-api' ) . '</tt>';
        $help['duplicates'] .= '</p>';
        $help['duplicates'] .= '<h3>' . __('Examples', 'datafeedr-api' ) . '</h3>';
        $help['duplicates'] .= '<p>';
        $help['duplicates'] .= '<tt>' . __('image</tt> - Exclude items which have the same image URL.', 'datafeedr-api' ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('name image</tt> - Exclude items with the same name AND the same image URL.', 'datafeedr-api' ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('name|image</tt> - Exclude items with the same name OR the same image URL.', 'datafeedr-api' ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('merchant_id name|image</tt> - Exclude items which have the same merchant id AND (product name OR image URL).', 'datafeedr-api' ) . '<br />';
        $help['duplicates'] .= '</p>';
        $help['duplicates'] .= $this->help_tip( __( 'By excluding duplicates, you will eliminate all but one item. For example, if 20 products have identical image URLs and you exclude duplicates matching the <strong>image</strong> field, <em>one</em> item will be returned and 19 items will be excluded.', 'datafeedr-api' ) );


        if (isset($help[$field])) {
            return $help[$field];
        } else {
            return '';
        }

    } // help()

}
