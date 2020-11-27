<?php



class Dfrapi_SearchForm
{
    function fields() {
        $opFulltext = array(
            'contain'       => __( 'contains', DFRAPI_DOMAIN ),
            'not_contain'   => __( 'doesn\'t contain', DFRAPI_DOMAIN ),
            'start'         => __( 'starts with', DFRAPI_DOMAIN ),
            'end'           => __( 'ends with', DFRAPI_DOMAIN ),
            'match'         => __( 'matches', DFRAPI_DOMAIN )
        );
        $opFulltextExact = array_merge($opFulltext, array(
            'is'  => __( 'is', DFRAPI_DOMAIN )
        ));
        $opRange = array(
            'eq'      => __( 'equal to', DFRAPI_DOMAIN ),
            'lt'      => __( 'less than', DFRAPI_DOMAIN ),
            'lte'     => __( 'less than or equal to', DFRAPI_DOMAIN ),
            'gt'      => __( 'greater than', DFRAPI_DOMAIN ),
            'gte'     => __( 'greater than or equal to', DFRAPI_DOMAIN ),
            'between' => __( 'between', DFRAPI_DOMAIN )
        );
        $opIs = array(
            'is'     => __( 'is', DFRAPI_DOMAIN )
        );
        $opIsIsnt = array(
            'is'     => __( 'is', DFRAPI_DOMAIN ),
            'is_not' => __( 'isn\'t', DFRAPI_DOMAIN )
        );
        $opYesNo = array(
            'yes'  => __( 'yes', DFRAPI_DOMAIN ),
            'no'   => __( 'no', DFRAPI_DOMAIN )
        );

	    $sortOpts = array(
		    ''              => __( 'Relevance', DFRAPI_DOMAIN ),
		    '+price'        => __( 'Price Ascending', DFRAPI_DOMAIN ),
		    '-price'        => __( 'Price Descending', DFRAPI_DOMAIN ),
		    '+saleprice'    => __( 'Sale Price Ascending', DFRAPI_DOMAIN ),
		    '-saleprice'    => __( 'Sale Price Descending', DFRAPI_DOMAIN ),
		    '+finalprice'   => __( 'Final Price Ascending', DFRAPI_DOMAIN ),
		    '-finalprice'   => __( 'Final Price Descending', DFRAPI_DOMAIN ),
		    '+salediscount' => __( 'Discount Ascending', DFRAPI_DOMAIN ),
		    '-salediscount' => __( 'Discount Descending', DFRAPI_DOMAIN ),
		    '+merchant'     => __( 'Merchant', DFRAPI_DOMAIN ),
		    '+time_created' => __( 'Date Created Ascending', DFRAPI_DOMAIN ),
		    '-time_created' => __( 'Date Created Descending', DFRAPI_DOMAIN ),
		    '+time_updated' => __( 'Last Updated Ascending', DFRAPI_DOMAIN ),
		    '-time_updated' => __( 'Last Updated Descending', DFRAPI_DOMAIN ),
		    '+_id'          => __( 'Product ID Ascending', DFRAPI_DOMAIN ),
		    '-_id'          => __( 'Product ID Descending', DFRAPI_DOMAIN ),
	    );

        return array(
            array(
                'title' => __( 'Any Field', DFRAPI_DOMAIN ),
                'name' => 'any',
                'input' => 'text',
                'operator' => $opFulltext,
                'help' => $this->help('any')
            ),
            array(
                'title' => __( 'Product Name', DFRAPI_DOMAIN ),
                'name' => 'name',
                'input' => 'text',
                'operator' => $opFulltextExact,
                'help' => $this->help('name')
            ),
	        array(
		        'title' => __( 'Brand', DFRAPI_DOMAIN ),
		        'name' => 'brand',
		        'input' => 'text',
		        'operator' => $opFulltextExact,
		        'help' => $this->help('brand')
	        ),
	        array(
		        'title' => __( 'Color', DFRAPI_DOMAIN ),
		        'name' => 'color',
		        'input' => 'text',
		        'operator' => $opFulltextExact,
		        'help' => $this->help('color')
	        ),
            array(
                'title' => __( 'Description', DFRAPI_DOMAIN ),
                'name' => 'description',
                'input' => 'text',
                'operator' => $opFulltext,
                'help' => $this->help('description')
            ),
            array(
                'title' => __( 'Tags', DFRAPI_DOMAIN ),
                'name' => 'tags',
                'input' => 'text',
                'operator' => array(
                    'in'     => __( 'contain', DFRAPI_DOMAIN ),
                    'not_in' => __( 'don\'t contain', DFRAPI_DOMAIN )
                ),
                'help' => $this->help('tags')
            ),
            array(
                'title' => __( 'Category', DFRAPI_DOMAIN ),
                'name' => 'category',
                'input' => 'text',
                'operator' => $opFulltext,
                'help' => $this->help('category')
            ),
            array(
                'title' => __( 'Product Type', DFRAPI_DOMAIN ),
                'name' => 'type',
                'input' => 'select',
                'options' => array(
                    'products' => __( 'Product', DFRAPI_DOMAIN ),
                    'coupons' => __( 'Coupon', DFRAPI_DOMAIN )
                ),
                'operator' => $opIs,
                'help' => $this->help('type')
            ),
            array(
                'title' => __( 'Currency', DFRAPI_DOMAIN ),
                'name' => 'currency',
                'input' => 'select',
                'options' => array(
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
                'help' => $this->help('currency')
            ),
            array(
                'title' => __( 'Price', DFRAPI_DOMAIN ),
                'name' => 'price',
                'input' => 'range',
                'operator' => $opRange,
                'help' => $this->help('price')
            ),
            array(
                'title' => __( 'Sale Price', DFRAPI_DOMAIN ),
                'name' => 'saleprice',
                'input' => 'range',
                'operator' => $opRange,
                'help' => $this->help('saleprice')
            ),
            array(
                'title' => __( 'Final Price', DFRAPI_DOMAIN ),
                'name' => 'finalprice',
                'input' => 'range',
                'operator' => $opRange,
                'help' => $this->help('finalprice')
            ),
            array(
                'title' => __( 'Network', DFRAPI_DOMAIN ),
                'name' => 'source_id',
                'input' => 'network',
                'operator' => $opIsIsnt,
                'help' => $this->help('source_id')
            ),
            array(
                'title' => __( 'Merchant', DFRAPI_DOMAIN ),
                'name' => 'merchant_id',
                'input' => 'merchant',
                'operator' => $opIsIsnt,
                'help' => $this->help('merchant_id')
            ),
            array(
                'title' => __( 'On Sale', DFRAPI_DOMAIN ),
                'name' => 'onsale',
                'input' => 'none',
                'operator' => $opYesNo,
                'help' => $this->help('onsale')
            ),
            array(
                'title' => __( 'Has Direct URL', DFRAPI_DOMAIN ),
                'name' => 'direct_url',
                'input' => 'none',
                'operator' => $opYesNo,
                'help' => $this->help('direct_url')
            ),
            array(
                'title' => __( 'Discount', DFRAPI_DOMAIN ),
                'name' => 'salediscount',
                'input' => 'range',
                'operator' => $opRange,
                'help' => $this->help('salediscount')
            ),
            array(
                'title' => __( 'Has Image', DFRAPI_DOMAIN ),
                'name' => 'image',
                'input' => 'none',
                'operator' => $opYesNo,
                'help' => $this->help('image')
            ),
            array(
                'title' => __( 'Last Updated', DFRAPI_DOMAIN ),
                'name' => 'time_updated',
                'input' => 'range',
                'operator' => array('lt' => 'before', 'gt' => 'after', 'between' => 'between'),
                'help' => $this->help('time_updated')
            ),
            array(
                'title' => __( 'Limit', DFRAPI_DOMAIN ),
                'name' => 'limit',
                'input' => 'text',
                'operator' => array('is' => 'is'),
                'help' => $this->help('limit')
            ),
            array(
		        'title'    => __( 'Merchant Limit', DFRAPI_DOMAIN ),
		        'name'     => 'merchant_limit',
		        'input'    => 'select',
		        'options'  => array_filter( range( 0, 50 ), function ( $num ) {
			        return $num != 0;
		        } ),
		        'operator' => array( 'is' => 'is' ),
		        'help'     => $this->help( 'merchant_limit' )
	        ),
            array(
                'title' => __( 'Sort By', DFRAPI_DOMAIN ),
                'name' => 'sort',
                'input' => 'none',
                'operator' => $sortOpts,
                'help' => $this->help('sort')
            ),
            array(
                'title' => __( 'Exclude Duplicates', DFRAPI_DOMAIN ),
                'name' => 'duplicates',
                'input' => 'text',
                'operator' => array(
                    'is' => __( 'matching these fields', DFRAPI_DOMAIN ),
                ),
                'help' => $this->help('duplicates')
            )
        );
    }

	function defaults() {
		return array(
			'any'            => array( 'operator' => 'contain', 'value' => '' ),
			'name'           => array( 'operator' => 'contain', 'value' => '' ),
			'brand'          => array( 'operator' => 'contain', 'value' => '' ),
			'color'          => array( 'operator' => 'contain', 'value' => '' ),
			'type'           => array( 'value' => 'product' ),
			'currency'       => array( 'value' => 'USD' ),
			'price'          => array( 'operator' => 'between', 'value' => '0', 'value2' => '999999' ),
			'saleprice'      => array( 'operator' => 'between', 'value' => '0', 'value2' => '999999' ),
			'source_id'      => array( 'value' => array() ),
			'merchant_id'    => array( 'value' => array() ),
			'onsale'         => array( 'value' => '1' ),
			'direct_url'     => array( 'value' => '1' ),
			'image'          => array( 'value' => '1' ),
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
            'network'  => __( 'Select Networks', DFRAPI_DOMAIN ),
            'merchant' => __( 'Select Merchants', DFRAPI_DOMAIN )
        );
        $clear = __( 'Clear search', DFRAPI_DOMAIN );
        $ok = __( 'OK', DFRAPI_DOMAIN );
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
            $html  .= ' ' . sprintf(__( 'and %s more', DFRAPI_DOMAIN ), count($value) - count($names));
        }
        return $html;
    }

    function chooseBox($kind, $field, $index, $value) {
        $pfx = $this->inputPrefix($index);
        $value = implode(',', $this->ary($value));
        $choose = __( 'choose', DFRAPI_DOMAIN );
        return "
            <div class='dfrapi_choose_box' rel='{$kind}'>
                <span class='names'></span>
                <a class='button choose_{$kind}'>{$choose}</a>
                <input name='{$pfx}[value]' type='hidden' value=\"$value\"/>
            </div>
        ";
    }

    function ajaxHandler() {
        $command = $this->get($_POST, 'command');
        $value = $this->ary($this->get($_POST, 'value'));
        $this->useSelected = intval($this->get($_POST, 'useSelected', 1));

        switch($command) {
            case "choose_network":
                return $this->networksMerchantsPopup('network', $value);
            case "choose_merchant":
                return $this->networksMerchantsPopup('merchant', $value);
            case "names_network":
                return $this->networksMerchantsNames('network', $value);
            case "names_merchant":
                return $this->networksMerchantsNames('merchant', $value);
        }
        return "";
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
                $and = __( 'and', DFRAPI_DOMAIN );
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

        $loading = __( 'Loading, please wait', DFRAPI_DOMAIN );
        $add = __( 'add filter', DFRAPI_DOMAIN );
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
                case 'description':
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
                case 'direct_url':
                    $operator = ($operator == 'yes') ? '!EMPTY' : 'EMPTY';
                    $filters []= "{$fname} {$operator}";
                    break;
                case 'image':
                case 'thumbnail':
                    $operator = ($operator == 'yes') ? '!EMPTY' : 'EMPTY';
                    $filters []= "image {$operator}";
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
    			<strong>' . __( 'TIP:', DFRAPI_DOMAIN ) . '</strong> ' .
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
    				<a href="https://v4.datafeedr.com/node/620" target="_blank">' . __('Learn more', DFRAPI_DOMAIN ) . '</a> ' .
               __('about advanced uses of this field.', DFRAPI_DOMAIN ) . '
    			</em>
    		</p>
    	';
    }

    function help($field) {

        $help = array();

        // Any
        $help['any'] = '<h3>' . __('Any Field', DFRAPI_DOMAIN ) . '</h3>';
        $help['any'] .= '<p>' . __( 'Search all indexed text fields at once to return a broad set of results.', DFRAPI_DOMAIN ) . '</p>';
        $help['any'] .= '<p>' . __( 'The fields listed below are indexed as text fields and searchable using Any Field. Image, currency, and price fields are not searched.', DFRAPI_DOMAIN ) . '</p>';
        $help['any'] .= '<p><em>' . __( '*Note that not all merchants and networks provide every field. ', DFRAPI_DOMAIN ) . '</em></p>';
        $help['any'] .= '<p>' . __( 'Fields searched using Any Field:', DFRAPI_DOMAIN ) . '</p>';
        $help['any'] .= '<table width="100%" border="0" style="margin-bottom:20px"><tr><td width="33%" valign="top">accommodationtype<br />address<br />artist<br />author<br />bestsellers<br />brand<br />category<br />city<br />color<br />commissiontype<br />condition<br />country<br />county<br />description<br />destination<br />discount</td><td width="33%" valign="top">discounttype<br />ean<br />fabric<br />featured<br />flavour<br />gender<br />genre<br />isbn<br />language<br />location<br />manufacturer<br />manufacturerid<br />material<br />model<br />mpn<br />name</td><td width="33%" valign="top">offercode<br />offertype<br />platform<br />productnumber<br />promo<br />publisher<br />rating<br />region<br />size<br />sku<br />stars<br />state<br />subcategory<br />tags<br />upc<br />weight</td></tr></table>';
        $help['any'] .= $this->help_operators();

        // Name
        $help['name'] = '<h3>' . __('Product Name', DFRAPI_DOMAIN ) . '</h3>';
        $help['name'] .= '<p>' . __( 'Search by product name to narrow your results.', DFRAPI_DOMAIN ) . '</p>';
        $help['name'] .= $this->help_tip( __( 'Some merchants include color, size, gender, product codes, sale information and promotions in the product name field.', DFRAPI_DOMAIN ) );
        $help['name'] .= $this->help_operators();

        // Brand
        $help['brand'] = '<h3>' . __('Brand', DFRAPI_DOMAIN ) . '</h3>';
        $help['brand'] .= '<p>' . __( 'Search by brand name to get specific results. Not every item has a brand name.', DFRAPI_DOMAIN ) . '</p>';
        $help['brand'] .= $this->help_tip( __( 'Enter the shorter version of a brand name in this field. Omit words such as "Incorporated", "Limited" and their abbreviations like Inc., Ltd. and so on.', DFRAPI_DOMAIN ) );
        $help['brand'] .= $this->help_operators();

        // Color
        $help['color'] = '<h3>' . __('Color', DFRAPI_DOMAIN ) . '</h3>';
        $help['color'] .= '<p>' . __( 'Search by color to get specific results. Not every item has a color field.', DFRAPI_DOMAIN ) . '</p>';
//        $help['color'] .= $this->help_tip( __( 'Enter colors into this field.', DFRAPI_DOMAIN ) );
//        $help['color'] .= $this->help_operators();

        // Description
        $help['description'] = '<h3>' . __('Description', DFRAPI_DOMAIN ) . '</h3>';
        $help['description'] .= '<p>' . __( 'Search the description field for product attributes such as size, color, material, or usage.', DFRAPI_DOMAIN ) . '</p>';
        $help['description'] .= $this->help_tip( __( 'Most merchants provide a product description. Some product details may only appear in the description and not in any other field. However, some merchants duplicate the product name or product code in the description field without supplying additional details or leave the product description blank.', DFRAPI_DOMAIN ) );
        $help['description'] .= $this->help_operators();

        // Tags
        $help['tags'] = '<h3>' . __('Tags', DFRAPI_DOMAIN ) . '</h3>';
        $help['tags'] .= '<p>' . __( 'Limit search results based on product tag and keyword information.', DFRAPI_DOMAIN ) . '</p>';
        $help['tags'] .= $this->help_tip( __( 'This field contains data from various keyword-related fields provided by the merchant. This field does not always exist.', DFRAPI_DOMAIN ) );

        // Category
        $help['category'] = '<h3>' . __('Category', DFRAPI_DOMAIN ) . '</h3>';
        $help['category'] .= '<p>' . __( 'Limit search results based on category information.', DFRAPI_DOMAIN ) . '</p>';
        $help['category'] .= $this->help_tip( __( 'This field contains data from various category-related fields provided by the merchant. This field does not always exist.', DFRAPI_DOMAIN ) );
        $help['category'] .= $this->help_operators();

        // Type
        $help['type'] = '<h3>' . __('Product Type', DFRAPI_DOMAIN ) . '</h3>';
        $help['type'] .= '<p>' . __( 'Limit your search results to one type of item, either Product or Coupon.', DFRAPI_DOMAIN ) . '</p>';
        $help['type'] .= $this->help_tip( __( 'In order to use this filter, you must have already selected merchants that provide that type of item. For example, if you choose "Product type: Coupon" but have not selected any merchants that offer coupons, your search will return an error.', DFRAPI_DOMAIN ) );

        // Currency
        $help['currency'] = '<h3>' . __('Currency', DFRAPI_DOMAIN ) . '</h3>';
        $help['currency'] .= '<p>' . __( 'Limit your search results to items with a specific currency code.', DFRAPI_DOMAIN ) . '</p>';
        $help['currency'] .= $this->help_tip( __( 'Selecting a currency code is one way to limit your search results to items from a specific country. However, not every item has been given a currency code value by the merchant. Items without a currency code will be excluded from your search results.', DFRAPI_DOMAIN ) );

        // Price
        $help['price'] = '<h3>' . __('Price', DFRAPI_DOMAIN ) . '</h3>';
        $help['price'] .= '<p>' . __( 'Filter your search results based on price. Return products less than, greater than, or within a price range that you set.', DFRAPI_DOMAIN ) . '</p>';
        $help['price'] .= $this->help_tip( __( 'This field does not search on sale price. If you set a price range to less than 30, you will exclude an item with a regular price of 40 that is on sale for less than 30.', DFRAPI_DOMAIN ) );

        // Sale Price
        $help['saleprice'] = '<h3>' . __('Sale Price', DFRAPI_DOMAIN ) . '</h3>';
        $help['saleprice'] .= '<p>' . __( 'Filter your search results based on sale price. Return items less than, greater than, or within a sale price range that you set.', DFRAPI_DOMAIN ) . '</p>';
        $help['saleprice'] .= $this->help_tip( __( 'This field does not search on regular price.  If you set a sale price range to between 50 and 100, an item with a sale price of 40 will be excluded even if it\'s regular price matches the range you set.', DFRAPI_DOMAIN ) );

        // Final Price
        $help['finalprice'] = '<h3>' . __('Final Price', DFRAPI_DOMAIN ) . '</h3>';
        $help['finalprice'] .= '<p>' . __( 'Filter your search results based on the final price. Return items less than, greater than, or within a sale price range that you set.', DFRAPI_DOMAIN ) . '</p>';
        $help['finalprice'] .= $this->help_tip( __( 'The final price is the lower price when comparing the regular price and sale price field.', DFRAPI_DOMAIN ) );

        // Network
        $help['source_id'] = '<h3>' . __('Network', DFRAPI_DOMAIN ) . '</h3>';
        $help['source_id'] .= '<p>' . __( 'Limit your search results to items from one or more affiliate networks.', DFRAPI_DOMAIN ) . '</p>';
        $help['source_id'] .= $this->help_tip( __( 'Affiliate networks are generally country-specific. Using the Network filter is one way to limit your search results by country.', DFRAPI_DOMAIN ) );

        // Merchant
        $help['merchant_id'] = '<h3>' . __('Merchant', DFRAPI_DOMAIN ) . '</h3>';
        $help['merchant_id'] .= '<p>' . __( 'Limit your search results to items from one or more merchants.', DFRAPI_DOMAIN ) . '</p>';
        $help['merchant_id'] .= $this->help_tip( __( 'Using the Merchant filter is a quick way to exclude unrelated products and reduce the number of API requests made when building a Product Set. For example, if you are creating a Product Set related to cat products, exclude all merchants that only sell dog products.', DFRAPI_DOMAIN ) );

        // On Sale
        $help['onsale'] = '<h3>' . __('On Sale', DFRAPI_DOMAIN ) . '</h3>';
        $help['onsale'] .= '<p>' . __( 'Set this field to "<strong>yes</strong>" to return only items which are on sale. To exclude products which are on sale, set this field to "<strong>no</strong>".', DFRAPI_DOMAIN ) . '</p>';

        // Has Direct URL
        $help['direct_url'] = '<h3>' . __('Has Direct URL', DFRAPI_DOMAIN ) . '</h3>';
        $help['direct_url'] .= '<p>' . __( 'Limit your search results to items which have a direct URL (or which don\'t).', DFRAPI_DOMAIN ) . '</p>';
        $help['direct_url'] .= $this->help_tip( __( 'A "Direct URL" is a URL directly to the product page on the merchant\'s website. By default, the "Direct URL" is never used as the URL in your "Buy" links. However, if you need the products in your store to contain a "Direct URL" to the product page on the merchants\' websites, then you should use this filter. This is most useful if you are using Skimlinks (or similar service) to generate your affiliate links instead of the affiliate networks.', DFRAPI_DOMAIN ) );

        // Discount
        $help['salediscount'] = '<h3>' . __('Discount', DFRAPI_DOMAIN ) . '</h3>';
        $help['salediscount'] .= '<p>' . __( 'Limit your search results to items with a specified discount. Enter the number in terms of percentage (1 - 100) to indicate a discount less than, greater than, or between a given range. You do not need to enter the percentage sign.', DFRAPI_DOMAIN ) . '</p>';
        $help['salediscount'] .= $this->help_tip( __( 'To display, for example, only products that are on sale for a discount of 20% or more, choose the "greater than" operator and type "19".', DFRAPI_DOMAIN ) );

        // Has Image
        $help['image'] = '<h3>' . __('Has Image', DFRAPI_DOMAIN ) . '</h3>';
        $help['image'] .= '<p>' . __( 'Limit your search results to items which have an image (or which don\'t).', DFRAPI_DOMAIN ) . '</p>';
        $help['image'] .= $this->help_tip( __( 'Sometimes the image URL in the merchant\'s data feed is broken. Items with broken images will still return in search results even though there appears to be no image.', DFRAPI_DOMAIN ) );

        // Last Updated
        $help['time_updated'] = '<h3>' . __('Last Updated', DFRAPI_DOMAIN ) . '</h3>';
        $help['time_updated'] .= '<p>' . __( 'Filter products by the last time they were updated by the merchant. Enter an English textual datetime description using PHP\'s <a href="http://www.php.net/strtotime" target="_blank">strtotime()</a> function.', DFRAPI_DOMAIN ) . '</p>';
        $help['time_updated'] .= '<h3>' . __('Examples', DFRAPI_DOMAIN ) . '</h3>';
        $help['time_updated'] .= '<p>';
        $help['time_updated'] .= '<tt>' . __('last week', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['time_updated'] .= '<tt>' . __('2 days ago', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['time_updated'] .= '<tt>' . __('1 month ago', DFRAPI_DOMAIN ) . '</tt>';
        $help['time_updated'] .= '</p>';
        $help['time_updated'] .= '<p>' . __( '', DFRAPI_DOMAIN ) . '</p>';
        $help['time_updated'] .= $this->help_tip( __( 'An item\'s Last Updated date will not change if the merchant does not change any product information during a data feed update.', DFRAPI_DOMAIN ) );

        // Limit
        $help['limit'] = '<h3>' . __('Limit', DFRAPI_DOMAIN ) . '</h3>';
        $help['limit'] .= '<p>' . __( 'Limit the number of items returned in your search results. The maximum number of products that can be returned is 10,000.', DFRAPI_DOMAIN ) . '</p>';
        $help['limit'] .= $this->help_tip( __( 'Limiting the number of products returned helps reduce the number of API requests made during searching and updating Product Sets.', DFRAPI_DOMAIN ) );

        // Merchant Limit
        $help['merchant_limit'] = '<h3>' . __('Merchant Limit', DFRAPI_DOMAIN ) . '</h3>';
        $help['merchant_limit'] .= '<p>' . __( 'Limit the number of items returned per merchant. The number must be between 1 and 50.', DFRAPI_DOMAIN ) . '</p>';
        $help['merchant_limit'] .= $this->help_tip( __( 'This is useful to use when you have one merchant which has many products that match your search but other merchants have fewer of those same products and you want equal distribution between all merchants.', DFRAPI_DOMAIN ) );

        // Sort By
        $help['sort'] = '<h3>' . __('Sort By', DFRAPI_DOMAIN ) . '</h3>';
        $help['sort'] .= '<p>' . __( 'Change the sort criteria by which items are displayed in your search results.', DFRAPI_DOMAIN ) . '</p>';
        $help['sort'] .= $this->help_tip( __( 'By default, search results are sorted by relevance. You can change that to sort by price, sale price, discount, or last updated date in ascending order (lowest to highest) or descending order (highest to lowest). You can also sort by merchant, which lists items alphabetically by merchant name.', DFRAPI_DOMAIN ) );

        // Exclude Duplicates
        $help['duplicates'] = '<h3>' . __('Exclude Duplicates', DFRAPI_DOMAIN ) . '</h3>';
        $help['duplicates'] .= '<p>' . __( 'Exclude items that contain identical product names, image URLs, etc. Enter one or more terms from the list below. Separate terms by a space (meaning AND) or | (pipe symbol, meaning OR).', DFRAPI_DOMAIN ) . '<br />';
        $help['duplicates'] .= '<p>' . __( 'Enter one of these terms to exclude duplicates matching these fields:', DFRAPI_DOMAIN ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('name', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('brand', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('currency', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('price', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('saleprice', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('source_id', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('merchant_id', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('direct_url', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('onsale', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('image', DFRAPI_DOMAIN ) . '</tt><br />';
        $help['duplicates'] .= '<tt>' . __('thumbnail', DFRAPI_DOMAIN ) . '</tt>';
        $help['duplicates'] .= '</p>';
        $help['duplicates'] .= '<h3>' . __('Examples', DFRAPI_DOMAIN ) . '</h3>';
        $help['duplicates'] .= '<p>';
        $help['duplicates'] .= '<tt>' . __('image</tt> - Exclude items which have the same image URL.', DFRAPI_DOMAIN ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('name image</tt> - Exclude items with the same name AND the same image URL.', DFRAPI_DOMAIN ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('name|image</tt> - Exclude items with the same name OR the same image URL.', DFRAPI_DOMAIN ) . '<br />';
        $help['duplicates'] .= '<tt>' . __('merchant_id name|image</tt> - Exclude items which have the same merchant id AND (product name OR image URL).', DFRAPI_DOMAIN ) . '<br />';
        $help['duplicates'] .= '</p>';
        $help['duplicates'] .= $this->help_tip( __( 'By excluding duplicates, you will eliminate all but one item. For example, if 20 products have identical image URLs and you exclude duplicates matching the <strong>image</strong> field, <em>one</em> item will be returned and 19 items will be excluded.', DFRAPI_DOMAIN ) );


        if (isset($help[$field])) {
            return $help[$field];
        } else {
            return '';
        }

    } // help()

}
