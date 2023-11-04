<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class RunProcessController extends Controller
{
    public function index(Request $request)
    {
        $process = new Process(['ls']);
        $process->run();

        echo $process->getOutput();

    }
}
