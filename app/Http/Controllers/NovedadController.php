<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Storage;

class NovedadController extends Controller
{
	public function index(Request $request){
		$client = new Client();
		$crawler = $client->request('GET', 'http://www.copaipa.org.ar/category/Obra-Social-COPAIPA/');

		$items = [];
		foreach ($crawler->filter('.item-list') as $item) {
			$item = new Crawler($item);
			$a = $item->filter('.post-title')->children();
			$post_title = $a->text();
			$link = $a->extract(['href']);
			$image = $item->filter('.post-thumbnail > a > img')->image();
			$url = $image->getUri();
			$contents = file_get_contents($url);
			/*
			$name = 'novedades/'.substr($url, strrpos($url, '/') + 1);
			if(Storage::disk('local')->exists($name)){
				$contents = Storage::get($name);
			} else {
				$contents = file_get_contents($url);
				Storage::put($name, $contents);
			}
			*/
			$image = base64_encode($contents);
			
			$entry = $item->filter('.entry > p')->text();
			$items[]=[
				'post_title' => $post_title,
				'link' => $link,
				'entry' => $entry,
				'image' => $image,
			];
		}

		return response()->json($items,200);
	}
}