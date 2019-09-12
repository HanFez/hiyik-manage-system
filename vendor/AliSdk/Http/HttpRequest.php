<?php
namespace AliSdk\Http;
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
use AliSdk\Constant\HttpHeader;
use AliSdk\Constant\ContentType;
class HttpRequest
{
	protected  $url;
	protected  $method;
	protected  $appcode;
	protected  $headers = array();
	protected  $querys = array();
	protected  $bodys = array();

	function  __construct($url, $appcode, $method  )
	{
	    $this->url = $url;
	    $this->method = $method;
		$this->appcode = $appcode;
		if($method=="GET"){
			$this->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_TEXT);
		}else{
			$this->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_FORM);
		}
		$this->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_TEXT);
		$this->setHeader("Authorization", "APPCODE " . $appcode);

	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function setHeader($key, $value)
	{
		if (null == $this->headers) {
			$this->headers = array();
		}
		$this->headers[$key] = $value;
	}

	public function getHeader($key)
	{
		return $this->headers[$key];
	}

	public function removeHeader($key)
	{
		unset($this->headers[$key]);
	}

	public function getQuerys()
	{
		return $this->querys;
	}

	public function setQuery($key, $value)
	{
		if (null == $this->querys) {
			$this->querys = array();
		}
		$this->querys[$key] = $value;
	}
	
	 

	public function getQuery($key)
	{
		return $this->querys[$key];
	}

	public function removeQuery($key)
	{
		unset($this->querys[$key]);
	}

	public function getBodys()
	{
		return $this->bodys;
	}

	public function setBody($key, $value)
	{
		if (null == $this->bodys) {
			$this->bodys = array();
		}
		$this->bodys[$key] = $value;
	}

	public function getBody($key)
	{
		return $this->bodys[$key];
	}

	public function removeBody($key)
	{
		unset($this->bodys[$key]);
	}

	public function setBodyStream($value)
	{
		if (null == $this->bodys) {
			$this->bodys = array();
		}
		$this->bodys[""] = $value;
	}

	public function setBodyString($value)
	{
		if (null == $this->bodys) {
			$this->bodys = array();
		}
		$this->bodys[""] = $value;
	}

  

	public function getUrl()
	{
		return $this->url;
	}
	
	public function setUrl($url)
	{
		$this->url = $url;
	}
 

	public function getMethod()
	{
		return $this->method;
	}
	
	public function setMethod($method)
	{
		$this->method = $method;
	}

	public function getAppcode()
	{
		return $this->appcode;
	}
	
	public function setAppcode($appcode)
	{
		$this->appcode = $appcode;
	}

 
}