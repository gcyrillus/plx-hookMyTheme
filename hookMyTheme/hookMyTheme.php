<?php
  class hookMyTheme extends plxPlugin {	 

	const HOOKS = array(
            'IndexEnd',
			'ThemeTag',
			'ThemeEndBody',
			'ThemeEndHead',
        );  
		
    public function __construct($default_lang) {
	

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# Ajoute des hooks
            foreach(self::HOOKS as $hook) {
                $this->addHook($hook, $hook);
            }	
		
		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);	
		
		# limite l'accès à l'écran d'administration du plugin
        $this->setAdminProfil(PROFIL_ADMIN);

    }

 

	#fonctions des hooks
		
		#desc hook function
		public function IndexEnd() {
			$prependToTag = $this->aParams['tag1']['value'];		
			
			echo '<?php ';?>
				ob_start();
				eval($plxMotor->plxPlugins->callHook('ThemeTag')); # Hook Plugins  
				$output = str_replace('<?php echo $prependToTag; ?>', ob_get_clean(), $output);		
		 ?>
		  <?php
		

		}
		
		

		#desc hook function
		public function ThemeEndBody() {
			echo	$this->aParams['ThemeEndBody']['value'];	
			
		}	
		
		#desc hook function
		public function ThemeEndHead() {
			echo	$this->aParams['ThemeEndHead']['value'];	
			
		}
		
		
		#desc hook function
		public function ThemeTag() {
			echo	$this->aParams['tag2']['value'];
		}


			
	
	
}
?>