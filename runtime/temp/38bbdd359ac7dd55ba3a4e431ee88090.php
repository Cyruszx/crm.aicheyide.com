<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:120:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\public/../application/admin\view\vehiclemanagement\secondhandcar\edit.html";i:1532659011;s:88:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\layout\default.html";i:1531976707;s:85:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\meta.html";i:1531976707;s:87:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\script.html";i:1531976707;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !$config['fastadmin']['multiplenav']): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Sales_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-sales_id" data-rule="required" data-source="sales/index" class="form-control selectpage" name="row[sales_id]" type="text" value="<?php echo $row['sales_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Licenseplatenumber'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-licenseplatenumber" data-rule="required" class="form-control" name="row[licenseplatenumber]" type="text" value="<?php echo $row['licenseplatenumber']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Models_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-models_id" data-rule="required" data-source="models/index" class="form-control selectpage" name="row[models_id]" type="text" value="<?php echo $row['models_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Kilometres'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-kilometres" data-rule="required" class="form-control" name="row[kilometres]" type="number" value="<?php echo $row['kilometres']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Companyaccount'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-companyaccount" data-rule="required" class="form-control" name="row[companyaccount]" type="text" value="<?php echo $row['companyaccount']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Newpayment'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-newpayment" data-rule="required" class="form-control" name="row[newpayment]" type="number" value="<?php echo $row['newpayment']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Monthlypaymen'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-monthlypaymen" data-rule="required" class="form-control" name="row[monthlypaymen]" type="number" value="<?php echo $row['monthlypaymen']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Periods'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-periods" data-rule="required" class="form-control" name="row[periods]" type="number" value="<?php echo $row['periods']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Totalprices'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-totalprices" data-rule="required" class="form-control" name="row[totalprices]" type="number" value="<?php echo $row['totalprices']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Drivinglicense'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-drivinglicense" class="form-control" name="row[drivinglicense]" type="text" value="<?php echo $row['drivinglicense']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Vin'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-vin" class="form-control" name="row[vin]" type="number" value="<?php echo $row['vin']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Expirydate'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-expirydate" class="form-control" name="row[expirydate]" type="number" value="<?php echo $row['expirydate']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Annualverificationdate'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-annualverificationdate" class="form-control" name="row[annualverificationdate]" type="number" value="<?php echo $row['annualverificationdate']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Carcolor'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-carcolor" class="form-control" name="row[carcolor]" type="text" value="<?php echo $row['carcolor']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Aeratedcard'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-aeratedcard" class="form-control" name="row[aeratedcard]" type="text" value="<?php echo $row['aeratedcard']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Volumekeys'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-volumekeys" class="form-control" name="row[volumekeys]" type="number" value="<?php echo $row['volumekeys']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Parkingposition'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-Parkingposition" class="form-control" name="row[Parkingposition]" type="text" value="<?php echo $row['Parkingposition']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Shelf'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-shelf" class="form-control selectpicker" name="row[shelf]">
                <?php if(is_array($shelfList) || $shelfList instanceof \think\Collection || $shelfList instanceof \think\Paginator): if( count($shelfList)==0 ) : echo "" ;else: foreach($shelfList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['shelf'])?$row['shelf']:explode(',',$row['shelf']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Vehiclestate'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-vehiclestate" class="form-control" name="row[vehiclestate]" type="text" value="<?php echo $row['vehiclestate']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Note'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-note" class="form-control" name="row[note]" type="text" value="<?php echo $row['note']; ?>">
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>