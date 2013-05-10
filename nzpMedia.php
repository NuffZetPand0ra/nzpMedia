<?php
class nzpMediaObj{
    public $provider;
    public $ressource_id;
    public $width;
    public $height;
    function __construct($provider, $ressource_id, $width="200", $height="200"){
        $this->provider = $provider;
        $this->ressource_id = $ressource_id;
        $this->width = $width;
        $this->height = $height;
    }
    function embed(){
        return nzpMedia::embed($this);
    }
    function __get($name){
        switch($name){
            default:
                return $this->$name;
                break;
            case 'pattern':
                return nzpMedia::$providers[$this->provider]['pattern'];
                break;
            case 'format':
                return nzpMedia::$providers[$this->provider]['format'];
                break;
            case 'embed':
                return nzpMedia::embed($this);
                break;
            case 'src':
                return nzpMedia::getIframeSrc($this);
                break;
            case 'thumb':
                return nzpMedia::getThumbnailSrc($this);
                break;
        }
    }
}
class nzpMedia{

    static public $providers = array(
        "youtube"   => array(
            "pattern"   	=> '/https?:\/\/(?:www\.)?youtu\.?be(?:\-nocookie)?(?:\.com)?\/(?:watch\?v\=|embed\/+)?([A-Za-z0-9\-\_]+)/'
          , "format"    	=> '<iframe src="%1$s" width="%2$d" height="%3$d" frameborder="0" allowfullscreen></iframe>'
          , "src"       	=> 'http://youtube.com/embed/%1$s'
          , "thumb"     	=> 'http://i.ytimg.com/vi/%1$s/default.jpg'
		  , "api_url"		=> 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id=%1$s&key=%2$s'
		  , "api_key"		=> false
        )
      , "soundcloud"=> array(
            "pattern"   	=> '/(?:https?:\/\/w\.soundcloud\.com\/player\/\?url=http\%3A\%2F\%2Fapi\.soundcloud.com\%2F((?:tracks\%2F)?[0-9A-Za-z\-\/]+))|(?:https?:\/\/(?:api\.)?soundcloud\.com\/([A-Za-z0-9\-\/]+))/'
          , "format"    	=> '<iframe src="%1$s" width="%2$d" height="%3$d" scrolling="no" frameborder="no"></iframe>'
          , "src"       	=> 'https://w.soundcloud.com/player/?url=http%%3A%%2F%%2Fapi.soundcloud.com%%2F%1$s'
        )
      , "vimeo"=> array(
            "pattern"   	=> '/http:\/\/(?:player\.)?vimeo\.com\/(?:video\/)?([0-9]+)/'
          , "format"    	=> '<iframe src="%1$s" width="%2$d" height="%3$d" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'
          , "src"       	=> 'http://player.vimeo.com/video/%1$s'
		  , "api_url"		=> 'http://vimeo.com/api/v2/video/%1$s.json'
        )
      , "deviantart"=> array(
            "pattern"   	=> '/http:\/\/backend\.deviantart\.com[A-Za-z0-9]+param name=\"flashvars\" value=\"id=([0-9]+)/'
          , "format"    	=> '<object width="%2$d" height="%3$d"><param name="movie" value="http://backend.deviantart.com/embed/view.swf?1"><param name="flashvars" value="id=%1$s&width=1337"><param name="allowScriptAccess" value="always"><embed src="http://backend.deviantart.com/embed/view.swf?1" type="application/x-shockwave-flash" width="450" height="460" flashvars="id=%1$s&width=1337" allowscriptaccess="always"></embed></object>'
          , "src"       	=> 'http://player.vimeo.com/video/%1$s'
        )
    );

    static function getInfo($code, $return = "obj"){
        $matches = array();
        foreach(self::$providers as $provider=>$arr){
            if(preg_match($arr['pattern'], $code, $matches)){
                $obj = new nzpMediaObj($provider,end($matches));
                switch($obj->provider){
                    case 'soundcloud':
                        if(substr($obj->ressource_id,0,6) != "tracks" || (substr($obj->ressource_id,0,6) == 'tracks' && substr($obj->ressource_id,6,1))){
                            $obj->ressource_id = urlencode($obj->ressource_id);
                        }
                        $obj->height = 166;
                        break;
                }
                return $$return;
            }
        }
        return false;
    }
	static function addApiKey($provider,$key){
		self::$providers[$provider]['api_key'] = $key;
	}
	static function getApiData(nzpMediaObj $mediaObj){
		$url = self::$providers[$mediaObj->provider]['api_url'];
		$params = array(self::$providers[$mediaObj->provider]['api_url'],$mediaObj->ressource_id);
		if(isset(self::$providers[$mediaObj->provider]['api_key'])){
			$params[] = self::$providers[$mediaObj->provider]['api_key'];
		}
		$url = call_user_func_array('sprintf', $params);
		$c = curl_init();
		echo $url;
		curl_setopt_array($c, array(
			CURLOPT_RETURNTRANSFER => 1
		  , CURLOPT_URL => $url
		  , CURLOPT_HTTPHEADER => array("Referer:")
		  , CURLOPT_SSL_VERIFYPEER => false
		  , CURLOPT_FOLLOWLOCATION => true
		));
		if($output = curl_exec($c)){
			// die($output);
			return json_decode($output);
		}
	}
    static function embed(nzpMediaObj $mediaObj){
        $params = array(self::$providers[$mediaObj->provider]['format'], $mediaObj->src, $mediaObj->width, $mediaObj->height);
        return call_user_func_array('sprintf', $params);
    }
    static function getIframeSrc(nzpMediaObj $mediaObj){
        $params = array(self::$providers[$mediaObj->provider]['src'], $mediaObj->ressource_id);
        return call_user_func_array('sprintf', $params);
    }
    static function getThumbnailSrc(nzpMediaObj $mediaObj){
        $params = array(self::$providers[$mediaObj->provider]['thumb'], $mediaObj->ressource_id);
        return call_user_func_array('sprintf', $params);
    }
}
?>