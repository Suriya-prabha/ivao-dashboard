<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WhazzUpParser;

class WhazzUpController extends Controller
{
    // private WhazzUpParser $parser;
    // public function __construct(WhazzUpParser $parser)
    // {
    //     $this->parser = $parser;
    // }
    // public function summary()    { return response()->json($this->parser->getSummary()); }
    // public function pilots()     { return response()->json($this->parser->getPilots()); }
    // public function airports()   { return response()->json($this->parser->getAirportActivity()); }
    // public function atc()        { return response()->json($this->parser->getATC()); }
    // public function aircraft()   { return response()->json($this->parser->getAircraftTypes()); }

    protected $parser;

    public function __construct(WhazzUpParser $parser)
    {
        $this->parser = $parser;
    }

    public function summary()
    {
        return response()->json(
            $this->parser->getSummary()
        );
    }

    public function pilots()
    {
        return response()->json(
            $this->parser->getPilots()
        );
    }

    public function airports()
    {
        return response()->json(
            $this->parser->getAirportActivity()
        );
    }

    public function aircraft()
    {
        return response()->json(
            $this->parser->getAircraftTypes()
        );
    }

    public function atc()
    {
        return response()->json(
            $this->parser->getATC()
        );
    }
}
