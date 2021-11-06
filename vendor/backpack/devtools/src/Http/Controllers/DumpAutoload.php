<?php

namespace Backpack\Devtools\Http\Controllers;

use Alert;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DumpAutoload extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        exec('composer dump-autoload');

        Alert::success('Executed <code class="text-primary">composer dump-autoload</code>.')->flash();

        return redirect()->back();
    }
}
