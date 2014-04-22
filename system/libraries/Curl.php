<?php

class Curl
{
    private $_ch;
    private $response;
    private $request;

    // config from config.php
    public $options;

	public function __construct(){
		$this->init();
	}
	// initialize curl
	public function init()
	{
		try {
			$this->_ch = curl_init();
			$options = is_array($this->options) ? ($this->options + $this->_config) : $this->_config;
			$this->setOptions($options);
			$ch = $this->_ch;

		} catch (Exception $e) {
			throw new Exception('Curl not installed');
		}
	}
    // default config
    private $_config = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER         => true,
        CURLOPT_VERBOSE        => true,
        CURLOPT_AUTOREFERER    => true,         
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:5.0) Gecko/20110619 Firefox/5.0'
    );

    private function exec($url)
    {
        $this->setOption(CURLOPT_URL, $url);
        $this->response = curl_exec($this->_ch);
        if (!curl_errno($this->_ch)) {
            $header_size = curl_getinfo($this->_ch, CURLINFO_HEADER_SIZE);
            $body = substr($this->response, $header_size);
            curl_close($this->_ch);
            $this->response = $body;
            return $body;
        } else {
            $this->response = curl_error($this->_ch);
            throw new Exception(curl_error($this->_ch));
        }
        curl_close($this->_ch);
        $this->response = 'curl_error';
        return false;
    }

    public function get($url, $params = array())
    {
        $this->setOption(CURLOPT_HTTPGET, true);
        return $this->exec($this->buildUrl($url, $params));
    }

    public function post($url, $data = array())
    {
        $this->setOption(CURLOPT_POST, true);
        $this->setOption(CURLOPT_POSTFIELDS, $data);

        return $this->exec($url);
    }

    public function put($url, $data, $params = array())
    {
        // write to memory/temp
        $f = fopen('php://temp', 'rw+');
        fwrite($f, $data);
        rewind($f);

        $this->setOption(CURLOPT_PUT, true);
        $this->setOption(CURLOPT_INFILE, $f);
        $this->setOption(CURLOPT_INFILESIZE, strlen($data));
        
        return $this->exec($this->buildUrl($url, $params));
    }

    public function delete($url, $params = array())
    {
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->exec($this->buildUrl($url, $params));
    }

    public function buildUrl($url, $data = array())
    {
        $parsed = parse_url($url);
        isset($parsed['query']) ? parse_str($parsed['query'], $parsed['query']) : $parsed['query'] = array();
        $params = isset($parsed['query']) ? array_merge($parsed['query'], $data) : $data;
        $this->request = json_encode($params);
        $parsed['query'] = ($params) ? '?' . http_build_query($params) : '';
        if (!isset($parsed['path'])) {
            $parsed['path']='/';
        }

        $parsed['port'] = isset($parsed['port'])?':'.$parsed['port']:'';

        return $parsed['scheme'].'://'.$parsed['host'].$parsed['port'].$parsed['path'].$parsed['query'];
    }

    public function setOptions($options = array())
    {
        curl_setopt_array($this->_ch, $options);

        return $this;
    }

    public function setOption($option, $value)
    {
        curl_setopt($this->_ch, $option, $value);
        return $this;
    }

    public function setHeaders($header = array())
    {
        if ($this->isAssoc($header)) {
            $out = array();
            foreach ($header as $k => $v) {
                $out[] = $k .': '.$v;
            }
            $header = $out;
        }

        $this->setOption(CURLOPT_HTTPHEADER, $header);
        
        return $this;
    }

    private function isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function getError()
    {
        return curl_error($this->_ch);
    }

    public function getInfo()
    {
        return curl_getinfo($this->_ch);
    }

    public function clear()
    {
        $this->options = null;
        $this->init();
    }

    public function getHeaders()
    {
        $headers = array();

        $header_text = substr($this->response, 0, strpos($this->response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }
        }

        return $headers;
    }

}