<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('vmware');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
   <div class="col-xs-12 eqLogicThumbnailDisplay">
  <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
  <div class="eqLogicThumbnailContainer">
    <div class="cursor eqLogicAction logoPrimary" data-action="add">
		<i class="fas fa-plus-circle"></i>
        <br>
        <span>{{Ajouter}}</span>
    </div>
    <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
		<i class="fas fa-wrench"></i>
    <br>
    <span>{{Configuration}}</span>
	</div>
	<div class="cursor eqLogicAction logoSecondary" id="bt_healtvmware">
		<i class="fas fa-wrench"></i>
    <br>
    <span>{{Santé}}</span>
	</div>
	
  </div>
  <legend><i class="fas fa-table"></i> {{Mes serveurs ESXi/ machines virtuelles}}</legend>
	   <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
<div class="eqLogicThumbnailContainer">
	<?php
foreach ($eqLogics as $eqLogicEsxiHost) {
	if ($eqLogicEsxiHost->getConfiguration('type') == 'ESXi') {
		echo '<legend>' . $eqLogicEsxiHost->getHumanName(true) . '</legend>';
		echo '<div class="eqLogicThumbnailContainer">';
		//$opacity = ($eqLogicEsxiHost->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
		//echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogicEsxiHost->getId() . '" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
		$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
		echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
		// On affiche une image différente pour le serveur eSXi pour le répérer plus facilement
		echo '<img src="plugins/vmware/docs/assets/images/icone_esxi.jpg" height="105" width="95">';
		echo '<br>';
		echo '<span class="name">' . $eqLogicEsxiHost->getHumanName(true, true) . '</span>';
		echo '</div>';
		foreach ($eqLogics as $eqLogicVM) {
			if ($eqLogicVM->getConfiguration('type') == 'vm' && $eqLogicVM->getConfiguration('ESXiHostIpAddress') == $eqLogicEsxiHost->getConfiguration('ipAddress')) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo "<br>";
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
		}
		echo '</div>';
	}
}
?>
</div>
</div>

<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
    <div role="tabpanel" class="tab-pane active" id="eqlogictab">
      <br/>
	  <div class="row">
        <div class="col-sm-6">
    <form class="form-horizontal">
        <fieldset>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                <div class="col-sm-3">
                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement ESXi}}"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                <div class="col-sm-3">
                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                        <option value="">{{Aucun}}</option>
                        <?php
foreach (jeeObject::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                   </select>
               </div>
           </div>
	   <div class="form-group">
                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                <div class="col-sm-9">
                 <?php
                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                    echo '</label>';
                    }
                  ?>
               </div>
           </div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-9">
					<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
					<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
				</div>
			</div>
			   <div class="form-group" id="ESXiIpAddress">
				<label class="col-sm-3 control-label">{{Adresse IP de votre ESXi}}</label>
				<div class="col-sm-3">
					<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ipAddress" placeholder="Au format XXX.XXX.XXX.XXX"/>
				</div>
			</div>
			</div>
			   <div class="form-group">
				<label class="col-sm-3 control-label">{{Login}}</label>
				<div class="col-sm-3">
					<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="login"/>
				</div>
			</div>
			</div>
			   <div class="form-group">
				<label class="col-sm-3 control-label">{{Mot de passe}}</label>
				<div class="col-sm-3">
					<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="passwordSSH"/>
				</div>
			</div>
		</fieldset>
	</form>
	</div>

		<div class="col-sm-6">
			<form class="form-horizontal">
				<fieldset>
					<div class="form-group">
						<label class="col-sm-3 control-label">{{Type}}</label>
						<div class="col-sm-3">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="type"/>
						</div>
						<label class="col-sm-3 control-label">{{Hôte ESXi}}</label>
						<div class="col-sm-3">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="esxiHostfield"/>
						</div>
					</div>
					
					 <div class="form-group">
						<label class="col-sm-3 control-label">{{Adresse IP}}</label>
						<div class="col-sm-3">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ipAddressfield"/>
						</div>
						<label class="col-sm-3 control-label">{{Nb snap}}</label>
						<div class="col-sm-3">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="nbSnapfield"/>
						</div>
					</div>
				</div>
				</fieldset>
			</form>
		</div>			
</div>
      <div role="tabpanel" class="tab-pane" id="commandtab">
<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
<table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>{{Nom}}</th><th>{{Type}}</th><th>{{Configuration}}</th><th>{{Action}}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>
</div>

</div>
</div>

<?php include_file('desktop', 'template', 'js', 'template');?>
<?php include_file('core', 'plugin.template', 'js');?>