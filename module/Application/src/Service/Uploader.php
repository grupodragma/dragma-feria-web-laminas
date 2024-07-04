<?php

namespace Application\Service;

class Uploader {

    protected $ancho;
    protected $alto; 
    protected $escala; 
    protected $max_ancho; 
    protected $max_alto;
    protected $nuevo_ancho;
    protected $nuevo_alto;
    public $tojpg = false;
    
    public function upload($origen,$destino,$ar_dims = null){
        if(copy($origen, $destino)){
            if(is_array($ar_dims))$this->resize($origen,$destino, $ar_dims['maxwidth'], $ar_dims['maxheight']);
            return true;
        }
    }

    public function resize($ubicacion, $nueva_ubicacion, $maxw, $maxh) {
        $this->max_ancho = $maxw;
        $this->max_alto = $maxh;
        $dim = getimagesize($ubicacion);
        $this->ancho = $dim[0];
        $this->alto = $dim[1];
        $this->escala = $this->ancho / $this->alto;

        if ($this->ancho < $this->max_ancho && $this->alto < $this->max_alto) {
            $this->nuevo_alto = $this->alto;
            $this->nuevo_ancho = $this->ancho;
            $this->resizeImage($ubicacion, $nueva_ubicacion, $dim["mime"]);
            return;
        }

        $this->nuevo_alto = $this->max_alto;
        $this->nuevo_ancho = $this->max_alto * $this->escala;

        $this->resizeImage($ubicacion, $nueva_ubicacion, $dim["mime"]);

        $dim = getimagesize($nueva_ubicacion);
        $this->ancho = $dim[0];
        $this->alto = $dim[1];
        if ($this->ancho > $this->max_ancho) {
            $this->escala = $this->ancho / $this->alto;
            $this->nuevo_ancho = $this->max_ancho;
            $this->nuevo_alto = $this->max_ancho / $this->escala;
            $this->resizeImage($nueva_ubicacion, $nueva_ubicacion, $dim["mime"]);
        }
    }

    public function resizeImage($ubicacion, $nueva_ubicacion, $tipo = 'image/jpeg') {

        $nuevaimagen = imagecreatetruecolor($this->nuevo_ancho, $this->nuevo_alto);

        switch ($tipo) {
            case "image/jpeg":
                $fuente = imagecreatefromjpeg($ubicacion); //jpeg file
                imagecopyresampled($nuevaimagen, $fuente, 0, 0, 0, 0, $this->nuevo_ancho, $this->nuevo_alto, $this->ancho, $this->alto);
                break;
            case "image/gif":
                $fuente = imagecreatefromgif($ubicacion); //gif file
                imagecolortransparent($nuevaimagen, imagecolorallocatealpha($nuevaimagen, 0, 0, 0, 127));

                imagealphablending($nuevaimagen, false);
                imagesavealpha($nuevaimagen, true);
                if ($this->tojpg) {
                    $white = imagecolorallocate($nuevaimagen, 255, 255, 255);
                    imagefilledrectangle($nuevaimagen, 0, 0, $this->nuevo_ancho, $this->nuevo_alto, $white);
                }
                imagecopyresampled($nuevaimagen, $fuente, 0, 0, 0, 0, $this->nuevo_ancho, $this->nuevo_alto, $this->ancho, $this->alto);
                break;
            case "image/png":
                $fuente = imagecreatefrompng($ubicacion); //png file 
                imagealphablending($nuevaimagen, false);
                imagesavealpha($nuevaimagen, true);
                if ($this->tojpg) {
                    $white = imagecolorallocate($nuevaimagen, 255, 255, 255);
                    imagefilledrectangle($nuevaimagen, 0, 0, $this->nuevo_ancho, $this->nuevo_alto, $white);
                }
                imagecopyresampled($nuevaimagen, $fuente, 0, 0, 0, 0, $this->nuevo_ancho, $this->nuevo_alto, $this->ancho, $this->alto);
                break;
        }

        if ($this->tojpg) {
            imagejpeg($nuevaimagen, $nueva_ubicacion, 100);
        } else {
            switch ($tipo) {
                case "image/jpeg": imagejpeg($nuevaimagen, $nueva_ubicacion, 100); break;
                case "image/gif": imagegif($nuevaimagen, $nueva_ubicacion); break;
                case "image/png": imagepng($nuevaimagen, $nueva_ubicacion, 9); break;
            }
        }

        chmod($nueva_ubicacion, 0755);
        
        return true;
    }

}
