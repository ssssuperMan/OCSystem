<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-29
 * Time: 上午11:02
 */

namespace Server\CoreBase;


class HttpInput
{
    /**
     * http request
     * @var \swoole_http_request
     */
    public $request;

    /**
     * @param $request
     */
    public function set($request)
    {
        $this->request = $request;
    }

    /**
     * 重置
     */
    public function reset()
    {
        unset($this->request);
    }

    /**
     * postGet
     * @param $index
     * @param $xss_clean
     * @return string
     */
    public function postGet($index, $xss_clean = true)
    {
        return isset($this->request->post[$index])
            ? $this->post($index, $xss_clean)
            : $this->get($index, $xss_clean);
    }

    /**
     * post
     * @param $index
     * @param $xss_clean
     * @return string
     */
    public function post($index, $xss_clean = true)
    {
        $input_data = $this->request->post[$index] ?? '';
        if(empty($input_data)){
            $input_data = $this->getRawContent();
            $input_data = json_decode($input_data, TRUE) ?? '';
            if($input_data != ''){
                $input_data = $input_data[$index] ?? '';
            }
        }
        if ($xss_clean) {
//            return XssClean::getXssClean()->xss_clean($this->request->post[$index]??'');
            return XssClean::getXssClean()->xss_clean($input_data);
        } else {
//            return $this->request->post[$index]??'';
            return $input_data;
        }
    }

    /**
     * get
     * @param $index
     * @param $xss_clean
     * @return string
     */
    public function get($index, $xss_clean = true)
    {
        $input_data = $this->request->get[$index] ?? '';
        if(empty($input_data)){
            $input_data = $this->getRawContent();
            $input_data = json_decode($input_data, TRUE) ?? '';
            if($input_data != ''){
                $input_data = $input_data[$index] ?? '';
            }
        }
        if ($xss_clean) {
//            return XssClean::getXssClean()->xss_clean($this->request->get[$index]??'');
            return XssClean::getXssClean()->xss_clean($input_data);
        } else {
//            return $this->request->get[$index]??'';
            return $input_data;
        }
    }

    /**
     * getPost
     * @param $index
     * @param $xss_clean
     * @return string
     */
    public function getPost($index, $xss_clean = true)
    {
        return isset($this->request->get[$index])
            ? $this->get($index, $xss_clean)
            : $this->post($index, $xss_clean);
    }

    /**
     * 获取所有的post
     */
    public function getAllPost($type = 'json')
    {
        $input_data = $this->request->post ?? [];
        if(!empty($input_data)){
            return $input_data;
        }else{
            $input_data = $this->getRawContent();
            switch ($type){
                case 'json' : return json_decode($input_data, TRUE) ?? [];
                                break;
                case 'text' : return $input_data ?? '';
                                break;
                default     : return $input_data;
                                break;
            }
        }
    }

    /**
     * 获取所有的get
     */
    public function getAllGet($type = 'json')
    {
        $input_data = $this->request->get ?? [];
        if(!empty($input_data)){
            return $input_data;
        }else{
            $input_data = $this->getRawContent();
            switch ($type){
                case 'json' : return json_decode($input_data, TRUE) ?? [];
                    break;
                case 'text' : return $input_data ?? '';
                    break;
                default     : return $input_data;
                    break;
            }
        }
    }
    /**
     * 获取所有的post和get
     */
    public function getAllPostGet($type = 'json')
    {
        $input_data = array_merge($this->request->post ?? [], $this->request->get ?? []);
        if(!empty($input_data)){
            return $input_data;
        }else{
            $input_data = $this->getRawContent();
            switch ($type){
                case 'json' : return json_decode($input_data, TRUE) ?? [];
                    break;
                case 'text' : return $input_data ?? '';
                    break;
                default     : return $input_data;
                    break;
            }
        }
    }

    /**
     * @param $index
     * @param bool $xss_clean
     * @return array|bool|string
     */
    public function header($index, $xss_clean = true)
    {
        if ($xss_clean) {
            return XssClean::getXssClean()->xss_clean($this->request->header[$index]??'');
        } else {
            return $this->request->header[$index]??'';
        }
    }

    /**
     * getAllHeader
     * @return array
     */
    public function getAllHeader()
    {
        return $this->request->header;
    }

    /**
     * 获取原始的POST包体
     * @return mixed
     */
    public function getRawContent()
    {
        return $this->request->rawContent();
    }

    /**
     * cookie
     * @param $index
     * @param $xss_clean
     * @return string
     */
    public function cookie($index, $xss_clean = true)
    {
        if ($xss_clean) {
            return XssClean::getXssClean()->xss_clean($this->request->cookie[$index]??'');
        } else {
            return $this->request->cookie[$index]??'';
        }
    }

    /**
     * getRequestHeader
     * @param $index
     * @param $xss_clean
     * @return string
     */
    public function getRequestHeader($index, $xss_clean = true)
    {
        if ($xss_clean) {
            return XssClean::getXssClean()->xss_clean($this->request->header[$index]??'');
        } else {
            return $this->request->header[$index]??'';
        }
    }

    /**
     * 获取Server相关的数据
     * @param $index
     * @param bool $xss_clean
     * @return array|bool|string
     */
    public function server($index, $xss_clean = true)
    {
        if ($xss_clean) {
            return XssClean::getXssClean()->xss_clean($this->request->server[$index]??'');
        } else {
            return $this->request->server[$index]??'';
        }
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->request->server['request_method'];
    }

    /**
     * @return mixed
     */
    public function getRequestUri()
    {
        if (array_key_exists('query_string', $this->request->server)) {
            return $this->request->server['request_uri'] . "?" . $this->request->server['query_string'];
        } else {
            return $this->request->server['request_uri'];
        }
    }

    /**
     * @return mixed
     */
    public function getPathInfo()
    {
        return $this->request->server['path_info'];
    }

    /**
     * 文件上传信息
     * Array
     * (
     *   [name] => facepalm.jpg
     *   [type] => image/jpeg
     *   [tmp_name] => /tmp/swoole.upfile.n3FmFr
     *   [error] => 0
     *   [size] => 15476
     * )
     * @return mixed
     */
    public function getFiles()
    {
        return $this->request->files;
    }
}