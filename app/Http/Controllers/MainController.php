<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{

    public function main() 
	{
		return view("layouts.app");
	}
	
	public function avatar(string $hash) 
	{
		$size = 100;
		$icon = new Jdenticon\Identicon();
		$icon->setValue($hash);
		$icon->setSize($size);
		$style = new Jdenticon\IdenticonStyle();
		$style->setBackgroundColor("#21232a");
		$icon->setStyle($style);
		$icon->displayImage("png");
		return response("")->header("Content-Type", "image/png");
	}
	  
}
