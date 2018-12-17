<?php
class Loader extends CI_Controller {	
	
	private $modified_time = 0;

    var $asset_output;
    var $type;
    var $files;
    var $asset_path;
    var $ext;
    var $content_type;
    
	
	public function __construct()
	{
		parent::__construct();
		$this->load->driver('minify');	
		$this->modified_time = 0; 
	}
    
    public function js($files)
    {
        $this->type = 'js';
        $this->files = $files;         
        $this->asset_path = $this->config->item('js_path');
        $this->ext = '.js';
        $this->content_type = 'text/javascript';

        $this->_output();
    }

    public function css($files)
    {
        $this->type = 'css';
        $this->files = $files;       
        $this->asset_path = $this->config->item('css_path');
        $this->ext = '.css';
        $this->content_type = 'text/css';

        $this->_output();
    }

    public function _output()
    {
    	$this->files = urldecode($this->files); 
    	 
        $files_array = explode("|", $this->files);       
        $files = array(); 
          
        foreach ($files_array as $key => $file)
        {
            //replace chars for folder separation, replace ~ with /            
            $file = str_replace('~', '/', $file);            
            $files[] = $this->asset_path . $file . $this->ext;
            if (file_exists($this->asset_path . $file . $this->ext))
            {            
                $this->modified_time = max(filemtime($this->asset_path . $file . $this->ext), $this->modified_time);
            }
       	}
 		
       	$this->asset_output = $this->minify->combine_files($files);
        
        //gzip
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            if (stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
                $this->asset_output = gzencode($this->asset_output);
                header('Content-encoding: gzip');
            } else if (stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false) {
                $this->asset_output = gzdeflate($this->asset_output);
                header('Content-encoding: deflate');
            }
        }

        //headers
        header('Content-type: ' . $this->content_type);
        header('Last-modified: ' . date('r', $this->modified_time));
        header('Expires: ' . date('r', time() + 2592000));
        header('Content-length: ' . strlen($this->asset_output));

        echo $this->asset_output;
    }
}

/* End of file loader.php */
/* Location: ./system/application/controllers/loader.php */