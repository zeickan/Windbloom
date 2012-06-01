<?php

/*
Google Chart Class | PHP 5
jonathan.discipulo@wideout.com
Build 1.0
*/

// }}
// {{ GoogleChart
class GoogleChart {

    var $size;
    var $data;
    var $fill;
    var $multi;
    var $type;
    var $title;
    var $tcs;
    var $label;
    var $legend;
    var $color;
    var $api;
    var $url;

    // }}
    // {{ Constructor
    function __construct() {
        
        $this->api = "http://chart.apis.google.com/chart";
        return true;
        
    }
    
    // }}
    // {{ setType
    function setType( $type ) {
        
        /*
        
        Chart Types
        http://code.google.com/apis/chart/types.html
        
        Line charts (lc, ls, lxy)
        Bar charts (bhs, bvs, bhg, bvg)
        Pie charts (p, p3, pc)
        Venn diagrams (v)
        Scatter plots (s)
        Radar charts (r, rs)
        Maps (t)
        Google-o-meters (gom)
        QR codes (qr)
        
        */
        
        $this->type = "cht={$type}";
        
    }

    // }}
    // {{ setSize
    function setSize( $width, $height ) {
        
        $this->size = "chs={$width}x{$height}";
        
    }

    // }}
    // {{ setFill
    function setFill( $fill ) {
        
        $this->fill = "chf={$fill}";
        
    }

    // }}
    // {{ setMultiFill
    function setMultiFill( $multi ) {
        
        $this->multi = "chm={$multi}";
        
    }

    // }}
    // {{ setColor
    function setColor( $color ) {
        
        $this->color = "chco={$color}";
        
    }

    // }}
    // {{ setTitle
    function setTitle( $title ) {
        
        $this->title = "chtt={$title}";
        
    }

    // }}
    // {{ setTCS
    function setTCS( $tc, $ts ) {
        
        $this->tcs = "chts={$tc},{$ts}";
        
    }
    
    // }}
    // {{ setData
    function setData( $data ) {
        
        $this->data = "chd=t:{$data}";
        
    }

    // }}
    // {{ setLabel
    function setLabel( $label ) {
        
        $this->label = "chl={$label}";
        
    }

    // }}
    // {{ setLegend
    function setLegend( $legend ) {
        
        $this->legend = "chdl={$legend}";
        
    }

    // }}
    // {{ showURL
    function showURL() {
        
        $this->url = $this->api . "?" .
            $this->type . "&amp;" .
            $this->size . "&amp;" .
            $this->color . "&amp;" .
            $this->multi . "&amp;" .
            $this->fill . "&amp;" .
            $this->legend . "&amp;" .
            $this->title . "&amp;" .
            $this->tcs . "&amp;" .
            $this->data . "&amp;" .
            $this->label;

    }

    // }}
    // {{ showChart
    function showChart() {
        
        $this->showURL();
        echo "<img src=\"{$this->url}\" alt=\"{$this->title}\" title=\"{$this->title}\" border=\"0\" />";
        
    }

    function __destruct() {
        // reserved for codes to run when this object is destructed
    }

} 