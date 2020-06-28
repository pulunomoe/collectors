<?php

namespace Collectors\API;

class Collection
{
	public function index($request, $response)
	{
		$response->getBody()->write('Hello, world!');
		return $response;
	}
}
