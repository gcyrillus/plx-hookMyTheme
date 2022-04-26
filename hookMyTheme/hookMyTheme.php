<?php
  class hookMyTheme extends plxPlugin {	 

	const HOOKS = array(
			'IndexBegin',
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
		

		# initialisation variables
		$this->aParams['tag1'			]['value']= $this->getParam('tag1'			)	=='' ? '' : $this->getParam('tag1'			);
		$this->aParams['tag2'			]['value']= $this->getParam('tag2'			)	=='' ? '' : $this->getParam('tag2'			);
		$this->aParams['ThemeEndBody'	]['value']= $this->getParam('ThemeEndBody'	)	=='' ? '' : $this->getParam('ThemeEndBody'	);
		$this->aParams['ThemeEndHead'	]['value']= $this->getParam('ThemeEndHead'	)	=='' ? '' : $this->getParam('ThemeEndHead'	); 

    }


        // désactive de force la compression gzip si activée pour une compatibilité des plugins usant du hook indexEnd() ou hook perso similaire dans les templates
        public function  IndexBegin() {
            echo '<?php ';
?>
        $plxMotor->aConf['gzip'] ='0';
            ?>
<?php           
        }
 

	#fonctions des hooks
		
		#desc hook function
		public function IndexEnd() {
			$prependToTag =$this->aParams['tag1']['value'];	
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
