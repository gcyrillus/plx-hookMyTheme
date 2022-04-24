<?php if(!defined('PLX_ROOT')) exit; 
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);

	
    if(!empty($_POST)) {
        $plxPlugin->setParam('tag1', $_POST['tag1'], 'cdata');
        $plxPlugin->setParam('tag2', $_POST['tag2'], 'cdata');
		$plxPlugin->setParam('ThemeEndHead', $_POST['ThemeEndHead'], 'cdata');
		$plxPlugin->setParam('ThemeEndBody', $_POST['ThemeEndBody'], 'cdata');
		$plxPlugin->setParam('template', $_POST['template'], 'cdata');
		$plxPlugin->setParam('load', $_POST['load'], 'cdata');
        $plxPlugin->saveParams();
		header('Location: parametres_plugin.php?p='.$plugin);
	exit;
    }
# Controle de l'accès à la page en fonction du profil de l'utilisateur connecté
$plxAdmin->checkProfil(PROFIL_ADMIN);		

$tpl= $plxPlugin->getParam('template');

$style = $plxAdmin->aConf['style'];
$filename = realpath(PLX_ROOT.$plxAdmin->aConf['racine_themes'].$style.'/'.$tpl);
if(!preg_match('#^'.str_replace('\\', '/', realpath(PLX_ROOT.$plxAdmin->aConf['racine_themes'].$style.'/').'#'), str_replace('\\', '/', $filename))) {
	$tpl='home.php';
}
$filename = realpath(PLX_ROOT.$plxAdmin->aConf['racine_themes'].$style.'/'.$tpl);

# On teste l'existence du thème
if(empty($style) OR !is_dir(PLX_ROOT.$plxAdmin->aConf['racine_themes'].$style)) {
	plxMsg::Error(L_CONFIG_EDITTPL_ERROR_NOTHEME);
	exit;
}



# On récupère les fichiers templates du thèmes
$aTemplates=array();
function listFolderFiles($dir, $include, $root=''){
	$content = array();
	$ffs = scandir($dir);
	foreach($ffs as $ff){
		if($ff!='.' && $ff!='..') {
			$ext = strtolower(strrchr($ff,'.'));
			if(!is_dir($dir.'/'.$ff) AND is_array($include) AND in_array($ext,$include)) {
				$f = str_replace($root, '', PLX_ROOT.ltrim($dir.'/'.$ff,'./'));
				$content[$f] = $f;
			}
			if(is_dir($dir.'/'.$ff))
				$content = array_merge($content, listFolderFiles($dir.'/'.$ff,$include,$root));
		}
	}
	return $content;
}
$root = PLX_ROOT.$plxAdmin->aConf['racine_themes'].$style;
$aTemplates=listFolderFiles($root, array('.php','.css','.htm','.html','.txt','.js','.xml'), $root);

# On récupère le contenu du fichier template
$content = '';
if(file_exists($filename) AND filesize($filename) > 0) {
	if($f = fopen($filename, 'r')) {
		$content = fread($f, filesize($filename));
		fclose($f);
	}
}
?>


<style>


/* feuille de style spécifique */
*{
	margin:0;
	padding:0;
	box-sizing:border-box;
}
form.HookMyTheme {
  display:grid;
  grid-template-columns:1fr 1fr;
}
p{
	margin:0;
}
fieldset {
  display:flex;
  flex-direction:column;
  margin:1em;
  background:#fafafa;
  border:solid 1px;
  border-radius:0.5em;
}
legend {
  margin-left:2em;
  background:lightgray;
  padding:0 0.5em;
  border-radius:0.25em;
}
label {
  font-weight:bold;
  text-indent:1em;
}
p {
  font-size:0.75em;
  color:#555;
  padding:0 1em;
}
input[type="submit"] {
  margin:1em auto;
}
textarea {
  margin:0.5em;
  flex-grow:1;
  max-width:calc(100% - 1em);
}
form>p {
  margin:auto;
  font-size:clamp(1em,3vw,20px);
}
#iptTpl {
  position:fixed;right:100vw;
}
div label{display:inline-block;}
div label + label {
  font-weight:normal;
  float:right;
  margin-right:0.5em;
  color:darkgreen
	}
#iptTpl ~fieldset label[for="iptTpl"]:before{
  content:'Voir';
}
#iptTpl:checked ~fieldset label[for="iptTpl"]:before{
  content:'Cacher';
}
#iptTpl ~fieldset label[for="iptTpl"]:after{
  content:'';
  display:inline-block;
  text-align:center;
  text-indent:0;
  font-size:1.5em;
  color:green;
  line-height:1rem;
  height:1.5rem;
  width:1.5rem;
  vertical-align:middle;
  margin:0 0.25em;
  box-shadow: inset 0 0 2px, inset 0 0 5px #bee,0 0 1px #bee;
}
#iptTpl:checked ~fieldset label[for="iptTpl"]:after{
  content:'\2713';
}
.tpl {
  display:none;
  background:#cfc;
}
#iptTpl:checked ~fieldset.tpl {
	display:flex
}
.r1 {
  grid-row:1;
}
.r2 {
  grid-row:2;
}
.c1 {
  grid-column:1;
}
.c2 {
  grid-column:2;
}
.r1-2 {
  grid-row:1 / span 2
}
p code {
  font-size:inherit;
  background:white;
}
textarea[readonly] {
  background:#efffef;
  color:darkblue;
}
</style>
<form action="parametres_plugin.php?p=<?php echo $plugin ?>" method="post" class="HookMyTheme">
 
 <!-- elements du formulaire -->
 
 <?php	
 if (plxUtils::strCheck($plxPlugin->getParam('load') !='')) { echo'<input type="checkbox" checked id="iptTpl" class="hidden">';	 }
 else {echo'<input type="checkbox"  id="iptTpl" class="hidden">';}
 ?>
	
	<fieldset class="r1 c1"><legend>Hook <code>&lt;/head></code></legend>
	<label>Contenu à ajouter avant la fermeture de head:</label> 
	<p>ex: &lt;link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css" ></p>
	<?php plxUtils::printArea('ThemeEndHead',plxUtils::strCheck($plxPlugin->getParam('ThemeEndHead')),0,5); ?>
	</fieldset>
	
	<fieldset class="r1 c2" ><legend>configuration custom hook</legend>
	<div><label>Tag à rechercher <i>(tag complet avec attributs sans script PhP)</i>:</label>   <label for="iptTpl"> les fichier du thémes</label></div>
	<?php plxUtils::printArea('tag1',plxUtils::strCheck($plxPlugin->getParam('tag1')),0,5); ?>
	
	<label>Contenu à ajouter:</label>
	<?php plxUtils::printArea('tag2',plxUtils::strCheck($plxPlugin->getParam('tag2')),0,5); ?>
	

	</fieldset>

	<fieldset class="r2 c1"><legend>Hook <code>&lt;/body></code></legend>
	<label>Contenu à ajouter avant la fermeture de body:</label>
	<?php plxUtils::printArea('ThemeEndBody',plxUtils::strCheck($plxPlugin->getParam('ThemeEndBody')),0,5); ?>
	</fieldset>
	
	<fieldset class="tpl r1-2 c1"><legend>Fichier du theme</legend>
	<div class="">
		<?php plxUtils::printSelectDir('template', $tpl , PLX_ROOT.$plxAdmin->aConf['racine_themes'].$style, 'no-margin', false) ?>
		<input name="load" type="submit" value="<?php echo L_CONFIG_EDITTPL_LOAD ?>" />
	</div>
    <script>
		let opts = document.querySelectorAll('#id_template option');

		let searchopt = '/ |lang|css|fonts|txt|img|xml|html/g';
		for( i=0;i<opts.length;i++) {
		  let test='';
		  let str = opts[i].getAttribute('value');
		  test = str.match(searchopt);
		  if(test!=null){ opts[i].parentNode.removeChild(opts[i])}
		  if(opts[i].hasAttribute('disabled')){ opts[i].parentNode.removeChild(opts[i])}
		  console.log(test)
		}	
	</script>

	<div class="grid">
		<div class="col sml-12">
			<label for="id_content"><?php echo L_CONTENT_FIELD ?>&nbsp;:</label>
			<p>Seuls les fichiers du thémes et balises HTML sans php sont exploitables par le hook. ex:<code>&lt;main class="main"&gt;</code>.</p>
			<p>Selectionner une balise contenant du PhP (ex:<code>&lt;article class="article" id="post-&lt;?php echo $plxShow->artId(); ?&gt;"&gt;</code>) sera sans effets.</p>
			<?php plxUtils::printArea('uselessHere',plxUtils::strCheck($content), 0, 20,'', 'full-width" readonly="true'); ?>
		</div>
	</div>
</fieldset>

	<p>
	<?php 
		echo plxToken::getTokenPostMethod();?>	
		<label>Validation et enregistrement du formulaire: <input type="submit" name="submit" value="Enregistrer" /></label>
	</p>
</form>
<?php



?>
