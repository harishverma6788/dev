<?php

use Cake\Routing\Router;
?>
<div class="page-header tt">
    <div class="page-title">
        <h3><?= __('New Bonus') ?></h3>
    </div>
    <!-- /Page Stats -->
</div>
<!-- /Page Header -->

<!--=== Page Content ===-->
<!--=== Full Size Inputs ===-->
<?= $this->Form->create($bonus, ['class' => 'form-horizontal ']) ?>	
<input type="hidden" name="customer_search_val" id="customer_search_val" value="">
<input type="hidden" name="agent_search_val" id="agent_search_val" value="">
<div class="row">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header tt">
                <h4><!--<i class="icon-reorder"></i>--><?= __('Bonus') ?></h4>
            </div>
            <div class="widget-content">

                <div class="form-group col-md-12 error-validation">
                    <label class="col-md-3 control-label email"><?= __('Bonous In/Out') ?></label>
                    <div class="col-md-5" >
                        <?php $optionsType = ['in' => 'In', 'out' => 'Out'];
                        echo $this->Form->input('type', ['templates' => ['inputContainer' => '{{content}}'], 'options' => $optionsType, 'empty' => __('--Select Type--'), 'class' => 'form-control superior_box', 'onchange' => 'javascript:inoutbonous(this);', 'id' => 'btype', 'label' => false, 'div' => false]);
                        ?>

                    </div>
                </div>

                <div class="form-group col-md-12 error-validation" id="in" style="display:none">
                    <label class="col-md-3 control-label email"><?= __('From Customer') ?></label>
					<?php if (!empty($options1)) { ?>
						<input type="hidden" value="<?Php echo $last_id; ?>" id="last_cust_id" />
					<?php } ?>
                    <div class="col-md-5 reuiredclass" id="customer_list">
                        <select id='customerid' name='customer_id' class='custom-select' onchange="javascript:showvaluec(this);">
							<option value=''>--Select Customer--</option>
							<?php //print_r($options1); ?>
							<?php if (!empty($options1)) {
							$check_array = array();
									foreach ($options1 as $customer_id => $customer_value) {
									//	print_r();
										if(!in_array($customer_id, $check_array)){ ?>
											<option value='<?php echo $customer_id; ?>'><?php echo $customer_value['value']; ?></option>
											<?php
												array_push($check_array,$customer_id);
										}
									}
								}
						?>
						  </select>
						  	<img src="/devloan/img/loader_new.gif" class="show_load" style="display:none;">
					</div>
                </div>             

                <div class="form-group col-md-12 error-validation">
                    <label class="col-md-3 control-label email"><?= __('Agent') ?><span style="color:#37444e;">*</span></label>
                    <div class="col-md-5 reuiredclass" id="agent_list">
					<?php if (!empty($options)) { ?>
						<input type="hidden" value="<?Php echo $last_agent_id; ?>" id="last_agnt_id" />
					<?php } ?>
                        <?php //echo $this->Form->input('agent_id', ['templates' => ['inputContainer' => '{{content}}'], 'options' => $options, 'empty' => __('--Select Agent--'), 'class' => 'form-control superior_box', 'onchange' => 'javascript:getreportdata(this);', 'div' => false, 'required' => true, 'id' => 'agentid', 'label' => false]); ?>
					<select id='agentid' name='agent_id' class='custom-select' onchange="javascript:getreportdata(this);">
							<option value=''>--Select Agent--</option>
							<?php //print_r($options); ?>
							<?php if (!empty($options)) {
							$check_array = array();
									foreach ($options as $agent_id => $agent_value) {
									//	print_r();
										if(!in_array($agent_id, $check_array)){ ?>
											<option value='<?php echo $agent_id; ?>'><?php echo $agent_value['value']; ?></option>
											<?php
												array_push($check_array,$agent_id);
										}
									}
								}
						?>
						  </select>
						  <img src="/devloan/img/loader_new.gif" class="show_load" style="display:none;">
                    </div>
                </div>

                <div class="form-group col-md-12 error-validation">
                    <label class="col-md-3 control-label email"><?= __('Bonus Amount : RM') ?><span style="color:#37444e;">*</span></label>
                    <div class="col-md-5">
                        <?php echo $this->Form->input('bonus_amount', ['templates' => ['inputContainer' => '{{content}}'], 'class' => 'form-control', 'div' => false, 'type' => 'text', 'label' => false, 'id' => 'bonousamount', 'required' => true]); ?>

                    </div>
                </div>

                <div class="form-group col-md-12 error-validation">
                    <label class="col-md-3 control-label email"><?= __('Release Date') ?><span style="color:#37444e;">*</span></label>
                    <div class="col-md-5">
                        <?php if ($current_user["group_id"] == 1) { ?>
                            <?php echo $this->Form->input('release_datee', ['templates' => ['inputContainer' => '{{content}}'], 'class' => 'form-control datepicker', 'required' => true, 'type' => 'text', 'div' => false, 'label' => false]); ?>
                        <?php } else { ?>
                            <?php echo $this->Form->input('release_datee', ['class' => 'form-control generateto', 'required' => true, 'type' => 'text', 'div' => false, 'label' => false]); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-2 col-md-offset-5">
                <?= $this->Form->button(__('Submit'), ['class' => 'submit btn btn-primary index', 'onclick' => "checkNumric();"]) ?>
            </div>
        </div>
    </div>
</div>



<?= $this->Form->end() ?>
<?php echo $this->Html->css('http://144.217.13.102/devloan/css/jquery-customselect.css'); ?>
<?php echo $this->Html->script('http://144.217.13.102/devloan/js/jquery-customselect.js'); ?>
<script>
    $(document).ready(function () {
        $("#agentout").select2({
            allowClear: true,
            placeholderOption: 'first'
        });

        $("#customerin").select2({
            allowClear: true,
            placeholderOption: 'first'
        });
        $("#btype").select2({
            allowClear: true,
            placeholderOption: 'first'
        });
        // $("#agentid").select2({
            // allowClear: true,
            // placeholderOption: 'first'
        // });
		$("#agentid").customselect();
		
		
		 var ajaxReq = 'ToCancelPrevReq'; // you can have it's value anything you like
    
			$( "#customer_list input" ).keyup(function(e) {
				$('#last_cust_id').val(0);
				//alert('ok');
			 setTimeout(function(){
						//$('#search_input p').remove();
						var this_val = $('#customer_list .custom-select input').val();
						$('#customer_search_val').val(this_val);
						//if(this_val == ' ' || this_val.length < 3){
							//$('.select2-results').remove();
						//}else{
							$('#customerid').html('');
							$('#customer_list ul li').each(function(){
								$(this).remove();
							});
						$('.select2-results').remove();
						$("#customer_list .show_load").show();
						ajaxReq = $.ajax({
								type: 'POST',
							data: {
								'last_cust_id': 0,
								'customer_name': this_val,
							},
							url: '<?php echo Router::url(array('controller' => 'money_collected_details', 'action' => 'getcustomers')); ?>',
							beforeSend : function() {
									if(ajaxReq != 'ToCancelPrevReq' && ajaxReq.readyState < 4) {
										ajaxReq.abort();
										//$('#search_input p').remove();
									}
							},
							success: function(result) {
									$("#customer_list .show_load").hide();
									//$("#agent").select2();
								var array = result.split('___');
								if(array[0] == ''){
									$('<li style="display:block!important;" class="" data-value="">No Results Found</li>').insertBefore('#customer_list .ajax_load_more');
								}else{
									$(array[0]).insertBefore('#customer_list .ajax_load_more');
									$('#customerid').append(array[1]);
								}
								$('#last_cust_id').val(array[2]);
							},
							error: function(xhr, ajaxOptions, thrownError) {
									if(thrownError == 'abort' || thrownError == 'undefined') return;
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						  }); 
						  				//	}
				},300);
			 
			});
		 $('body').on('click','#customer_list .ajax_load_more',function(e){
				setTimeout(function(){
					var last_cust_id = $('#last_cust_id').val();
					var agent_id = $('#agent').val();
					if(agent_id =='empty'){
						agent_id = '';
					}
					var this_val = $('#customer_list .custom-select input').val();
					//$('#customer').html('');
					var check_text = 0;
							$('#customer_list ul li').each(function(){
								//if( $(this).html() == 'No Results Found'){
									check_text = 1;
								//}
							});
						 $("#customer_list .show_load").show();
						ajaxReq = $.ajax({
								type: 'POST',
							data: {
								'last_cust_id': last_cust_id,
								'customer_name': this_val,
								'agent_id' : agent_id
							},
							url: '<?php echo Router::url(array('controller' => 'money_collected_details', 'action' => 'getcustomers')); ?>',
							beforeSend : function() {
									if(ajaxReq != 'ToCancelPrevReq' && ajaxReq.readyState < 4) {
										ajaxReq.abort();
									}
							},
							success: function(result) {
									$("#customer_list .show_load").hide();
									//$("#agent").select2();
									if(result == 'no data'){
									}else{
								var array = result.split('___');
								if(array[0] == '' && check_text == 0){
									$('<li style="display:block!important;" class="" data-value="">No Results Found</li>').insertBefore('#customer_list .ajax_load_more');
								}else{
									$(array[0]).insertBefore('#customer_list .ajax_load_more');
									$('#customerid').append(array[1]);
								}
								$('#last_cust_id').val(array[2]);
								}
							},
							error: function(xhr, ajaxOptions, thrownError) {
									if(thrownError == 'abort' || thrownError == 'undefined') return;
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						  }); 
				},200);
			  });
		
		
		
		$('.ajax_load_more').each(function(){
			var parent = $(this).parent();
			var count_li = 0;
			parent.find('li').each(function(){
				count_li++;
			});
			if(count_li < 2){
				$(this).remove();
			}
		 });
		  var ajaxReq = 'ToCancelPrevReq'; 
		$( "#agent_list input" ).keyup(function(e) {
				$('#last_agnt_id').val(0);
				//alert('ok');
			 setTimeout(function(){
						//$('#search_input p').remove();
						var this_val = $('#agent_list .custom-select input').val();
						$('#agent_search_val').val(this_val);
						var last_agnt_id = $('#last_agnt_id').val();
						//if(this_val == ' ' || this_val.length < 3){
							//$('.select2-results').remove();
						//}else{
							$('#agentid').html('');
							$('#agent_list ul li').each(function(){
								$(this).remove();
							});
						$('.select2-results').remove();
						$("#agent_list .show_load").show();
						ajaxReq = $.ajax({
								type: 'POST',
							data: {
								'last_agnt_id': 0,
								'agentname': this_val,
							},
							url: '<?php echo Router::url(array('controller' => 'money_collected_details', 'action' => 'getagents')); ?>',
							beforeSend : function() {
									if(ajaxReq != 'ToCancelPrevReq' && ajaxReq.readyState < 4) {
										ajaxReq.abort();
										//$('#search_input p').remove();
									}
							},
							success: function(result) {
									$("#agent_list .show_load").hide();
									//$("#agent").select2();
								var array = result.split('___');
								if(array[0] == ''){
									$('<li style="display:block!important;" class="" data-value="">No Results Found</li>').insertBefore('#agent_list .ajax_load_more');
								}else{
									$(array[0]).insertBefore('#agent_list .ajax_load_more');
									$('#agentid').append(array[1]);
								}
								$('#last_agnt_id').val(array[2]);
							},
							error: function(xhr, ajaxOptions, thrownError) {
									if(thrownError == 'abort' || thrownError == 'undefined') return;
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						  }); 
						  				//	}
				},300);
			 
			});
			
			$('body').on('click','#agent_list .ajax_load_more',function(e){
			 setTimeout(function(){
				  var last_agnt_id = $('#last_agnt_id').val();
				  var this_val = $('#agent_list .custom-select input').val();
				 // $('#customer').html('');
				  var check_text = 0;
							$('#agent_list ul li').each(function(){
								//if( $(this).html() == 'No Results Found'){
									check_text = 1;
								//}
							});
						 $("#agent_list .show_load").show();
						ajaxReq = $.ajax({
								type: 'POST',
							data: {
								'last_agnt_id': last_agnt_id,
								'agentname': this_val
							},
							url: '<?php echo Router::url(array('controller' => 'money-collected-details', 'action' => 'getagents')); ?>',
							beforeSend : function() {
									if(ajaxReq != 'ToCancelPrevReq' && ajaxReq.readyState < 4) {
										ajaxReq.abort();
									}
							},
							success: function(result) {
									$("#agent_list .show_load").hide();
									if(result == 'no data'){
										
									}else{
											var array = result.split('___');
											if(array[0] == '' && check_text == 0){
												
												$('<li style="display:block!important;" class="" data-value="">No Results Found</li>').insertBefore('#agent_list .ajax_load_more');
											}else{
												$(array[0]).insertBefore('#agent_list .ajax_load_more');
												$('#agentid').append(array[1]);
											}
											$('#last_agnt_id').val(array[2]);
									}
							},
							error: function(xhr, ajaxOptions, thrownError) {
									if(thrownError == 'abort' || thrownError == 'undefined') return;
									alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						  }); 
			 },200);
			  });
         $("#customerid").customselect();
        $(".datepicker").datepicker("destroy");
        $(".datepicker").datepicker({dateFormat: 'dd/mm/yy'});

    });
    $(".select_agent").hide();
    function checkvalue(e)
    {


        var selectedval = $("#group_id option:selected").val();
        if (selectedval == 3)
        {
            $(".select_agent").show();
        } else
        {
            $(".select_agent").hide();
        }
    }
    function getreportdata()
    {
        var selectedval = $("#agentid option:selected").val();
        $.ajax({
            type: "POST",
            url: "<?php echo Router::url(array('controller' => 'Reports', 'action' => 'getagentlastreportdata')) ?>",
            data: 'id=' + selectedval,
            success: function (response) {
                var returnedData = JSON.parse(response);
                var date = returnedData.last_generate_to;
                var t = date.split(/[- :]/);
                if (returnedData.last_generate_to != 'fromstart')
                {
                    $(".generateto").datepicker("destroy");
                    $(".generateto").datepicker(
                            {
                                minDate: new Date(t[0], t[1] - 1, parseInt(t[2]) + 1),
                                dateFormat: 'dd/mm/yy'
                            });
                } else
                {

                    $(".generateto").datepicker("destroy");
                    $(".generateto").datepicker({dateFormat: 'dd/mm/yy'});
                }
            }
        });
    }


    function checkNumric() {
        var amount = $("#bonousamount").val();

        if (!$.isNumeric(amount)) {

            document.getElementById("bonousamount").setCustomValidity("Please provide numeric value");
        } else {
            document.getElementById("bonousamount").setCustomValidity("");
        }
    }

    var agent = $('select#agentout option:selected').text();
var customer = $('select#customerin option:selected').text();
    function inoutbonous() {
   
                   
        var type = $("#btype").val();
        if (type == 'in') {
            $("#in").show();
             $('#customerin').prop('required', true);
           $("#agentout").val("");
        } else {

    $("#s2id_customerin .select2-chosen").text(customer);
            $('#customerin').removeAttr('required');
            $("#in").hide();
           // $("#out").show();
        //   $("#customerin").val("");
           

        }

    }
</script>


<!--Forms -->

