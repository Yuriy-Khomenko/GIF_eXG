<?php
/**********************************************************************************
								PASSPORT OF CLASS
 Name: GIF_eXG
 Current version: 1.081
 Appointment: resize gif image file with support animation and transparency
 Features: fast, stable and correct work with most files, ease of use
 
 History of modification:
  - 1.00 basic functionality
  - 1.01 bag fix
  - 1.02 fast resize, overall optization and first release
  - 1.03 bag fix (thanks for council of aAotD)
  - 1.04 small fix (support not standart file formats)
  - 1.05 fix (added: support new not standart file formats; optization code, thanks for council of AvrGavr)
  - 1.06 correct handling files with error sizes of local frame
  - 1.07 correct resampled (on request MasterShredder)
  - 1.08 timing fix
  - 1.081 small code fix
  
 Author: Yuriy Khomenko
 Year of development: 2013
 Country: Ukraine
 
 Developed and test:
  - PHP 5.3.13/5.5.4
  - GD 2.0.34/2.1.0
  - OS Windows/Linux
  
 Attention and comment: 
  - class can be used for personal and commercial purposes
  - class is allowed to change or modify
  - i will be glad if you class will come in handy
 
 How to use:
  1) require_once "gif_exg.php";	- include a library file
	
  2) $nGif = new GIF_eXG($source_file,$opt);	- create an instance of the class
		- $source_file: full path to the source file 
		- $opt: "1" - use optization
			    "0" - not use optization (file will retain the internal structure)
	
  3) $nGif->resize($dest_file,$new_width,$new_height,$symmetry,$resampled);	- public function for changing the size of (returns NULL on failure)
		- $dest_file: full path to the destination file
		- $new_width: new image width
		- $new_height: new image height
		- $symmetry:  "1" - preserve symmetry
				      "0" - not preserve symmetry
		- $resampled: "1" - use resampled
 					  "0" - not use resampled
						 
 Example:
 
 require_once "gif_exg.php";
 $nGif = new GIF_eXG("../image/src.gif",1);
 $nGif->resize("../image/dst1.gif",180,180,1,1);
 $nGif->resize("../image/dst2.gif",150,150,0,1);
 
**********************************************************************************/
class FRM {

    var $pos_x, $pos_y, $width_f, $height_f, $tr_frm = 0, $lc_mod, $gr_mod, $off_xy, $head, $lc_palet, $image;

    function FRM($lc_mod, $lc_palet, $image, $head, $pzs_xy, $gr_mod) {
        $this->lc_mod = $lc_mod;
        $this->lc_palet = $lc_palet;
        $this->image = $image;
        $this->head = $head;
        $this->pos_x = $pzs_xy[0];
        $this->pos_y = $pzs_xy[1];
        $this->width_f = $pzs_xy[2];
        $this->height_f = $pzs_xy[3];
        $this->gr_mod = $gr_mod;
        $this->tr_frm = ord($gr_mod[3]) & 1 ? 1 : 0;
    }

}

class GIF_eXG {

    private $gif, $pnt = 0, $gl_mn, $gl_palet, $gl_mod, $gl_mode, $int_w, $int_h, $au = 0, $er = 0, $nt = 0, $lp_frm = 0, $ar_frm = Array(), $gn_fld = Array(), $dl_frmf = Array(), $dl_frms = Array();

    function GIF_eXG($file_src, $opt) {
        $this->gif = file_get_contents($file_src);
        $this->gl_mn = $this->gtb(13);
        if (substr($this->gl_mn, 0, 3) != "GIF") {
            $this->er = 1;
            return 0;
        }$this->int_w = $this->rl_int($this->gl_mn[6] . $this->gl_mn[7]);
        $this->int_h = $this->rl_int($this->gl_mn[8] . $this->gl_mn[9]);
        if (($vt = ord($this->gl_mn[10])) & 128 ? 1 : 0) {
            $this->gl_palet = $this->gtb(pow(2, ($vt & 7) + 1) * 3);
        }$buffer_add = "";
	if($this->gif[$this->pnt] == "\x21"){		
       while ($this->gif[$this->pnt + 1] != "\xF9" && $this->gif[$this->pnt] != "\x2C") {
		switch ( $this->gif[$this->pnt + 1] ) {
			case "\xFE":
                $sum = 2;
                while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                    $sum+=$lc_i + 1;
                }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
				break;
			case "\xFF":
                $sum = 14;
                while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                    $sum+=$lc_i + 1;
                }$buffer_add.=$this->gtb($sum + 1);
				break;
			case "\x01":
                $sum = 15;
                while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                    $sum+=$lc_i + 1;
                }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
            
        }}$this->gl_mod = $buffer_add;
	}		
        while ($this->gif[$this->pnt] != "\x3B" && $this->gif[$this->pnt + 1] != "\xFE" && $this->gif[$this->pnt + 1] != "\xFF" && $this->gif[$this->pnt + 1] != "\x01") {
            $lc_mod;
            $lc_palet;
            $pzs_xy = Array();
            $head;
            $gr_mod;
            $this->lp_frm++;
            while ($this->gif[$this->pnt] != "\x2C") {
			  switch ($this->gif[$this->pnt + 1]) {
				case "\xF9":
                    $this->gn_fld[] = $this->gif[$this->pnt + 3];
                    $this->dl_frmf[] = $this->gif[$this->pnt + 4];
                    $this->dl_frms[] = $this->gif[$this->pnt + 5];
                    $gr_mod = $buffer_add = $this->gtb(8);
					break;
				case "\xFE":	
                    $sum = 2;
                    while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                        $sum+=$lc_i + 1;
                    }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
					break;
				case "\xFF":
                    $sum = 14;
                    while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                        $sum+=$lc_i + 1;
                    }if (substr($tmp_buf = $this->gtb($sum + 1), 3, 8) == "NETSCAPE") {
                        if (!$this->nt) {
                            $this->nt = 1;
                            $this->gl_mod.=$tmp_buf;
                        }
                    } else {
                        $buffer_add.=$tmp_buf;
                    }
					break;
				case "\x01":
                    $sum = 15;
                    while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                        $sum+=$lc_i + 1;
                    }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
              }
            }$lc_mod = $buffer_add;
            $pzs_xy[] = $this->ms_int(1, 2);
            $pzs_xy[] = $this->ms_int(3, 2);
            $pzs_xy[] = $this->ms_int(5, 2);
            $pzs_xy[] = $this->ms_int(7, 2);
            $head = $this->gtb(10);
			if((($pzs_xy[0] + $pzs_xy[2])-$this->int_w)>0){
				$head[1]= "\x00";
				$head[2]= "\x00";
				$head[5]= $this->int_raw($this->int_w);
				$head[6]= "\x00";
				
				$pzs_xy[0]=0;
				$pzs_xy[2]=$this->int_w;
			}
			if((($pzs_xy[1] + $pzs_xy[3])-$this->int_h)>0){
				$head[3]= "\x00";
				$head[4]= "\x00";
				$head[7]= $this->int_raw($this->int_h);
				$head[8]= "\x00";			
				$pzs_xy[1]=0;
				$pzs_xy[3]=$this->int_h;
			}		
            if ((ord($this->gif[$this->pnt - 1]) & 128 ? 1 : 0)) {
                $lc_i = pow(2, (ord($this->gif[$this->pnt - 1]) & 7) + 1) * 3;
                $lc_palet = $this->gtb($lc_i);
            }$sum = 0;
            $this->pnt++;
            while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                $sum+=$lc_i + 1;
            }$this->pnt--;
            $this->ar_frm[] = new FRM($lc_mod, $lc_palet, $this->gtb($sum + 2), $head, $pzs_xy, $gr_mod);
        }$buffer_add = "";
        while ($this->gif[$this->pnt] != "\x3B") {
		  switch ($this->gif[$this->pnt + 1]){
			case "\xFE":
                $sum = 2;
                while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                    $sum+=$lc_i + 1;
                }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
                if ($sum == 17) {
                    $this->au = 1;
                }
				break;
			case "\xFF":	
                $sum = 14;
                while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                    $sum+=$lc_i + 1;
                }$buffer_add.=$this->gtb($sum + 1);
				break;
			case "\x01":	
                $sum = 15;
                while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                    $sum+=$lc_i + 1;
                }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
          }
        }$this->gl_mode = $buffer_add;
        $this->gif = "";
    }

    private function gtb($n) {
        $b = substr($this->gif, $this->pnt, $n);
        $this->pnt+=$n;
        return $b;
    }

    private function rl_int($hw) {
        $z = ord($hw[1]) << 8;
        $c = ord($hw[0]);
        $x = $z | $c;
        return $x;
    }

    private function ms_int($g_f, $g_s) {
        return $this->rl_int(substr($this->gif, $this->pnt + $g_f, $g_s));
    }

    private function int_raw($t) {
        return chr($t & 255) . chr(($t & 0xFF00) >> 8);
    }

    private function cr_img($i) {
        return $this->gl_mn . $this->gl_palet . $this->gl_mod . $this->ar_frm[$i]->lc_mod . $this->ar_frm[$i]->head . $this->ar_frm[$i]->lc_palet . $this->ar_frm[$i]->image . "\x3B";
    }

    private function resize_img($b, $ind_f, $des) {
		$buf_n = round($this->ar_frm[$ind_f]->width_f * $des[0]);
        $n_width = $buf_n ? $buf_n : 1;
		$buf_n = round($this->ar_frm[$ind_f]->height_f * $des[1]);
        $n_height = $buf_n ? $buf_n : 1;
        $n_pos_x = round($this->ar_frm[$ind_f]->pos_x * $des[0]);
        $n_pos_y = round($this->ar_frm[$ind_f]->pos_y * $des[1]);
        $this->ar_frm[$ind_f]->off_xy = $this->int_raw($n_pos_x) . $this->int_raw($n_pos_y);
        $str_img = @imagecreatefromstring($b);
        if ($this->lp_frm == 1 || $des[3]) {
            $img_s = @imagecreatetruecolor($n_width, $n_height); 
        } else {
            $img_s = @imagecreate($n_width, $n_height);
        }if ($this->ar_frm[$ind_f]->tr_frm) {			
            $in_trans = @imagecolortransparent($str_img);
            if ($in_trans >= 0 && $in_trans < @imagecolorstotal($img_s)) {
                $tr_clr = @imagecolorsforindex($str_img, $in_trans);
            }if ($this->lp_frm == 1 || $des[3]) {
                $n_trans = @imagecolorallocatealpha($img_s, 255, 255, 255, 127);
            } else {
                $n_trans = @imagecolorallocate($img_s, $tr_clr['red'], $tr_clr['green'], $tr_clr['blue']);
            }@imagecolortransparent($img_s, $n_trans);
            @imagefill($img_s, 0, 0, $n_trans);
        }@imagecopyresampled($img_s, $str_img, 0, 0, 0, 0, $n_width, $n_height, $this->ar_frm[$ind_f]->width_f, $this->ar_frm[$ind_f]->height_f);
        @ob_start();
        @imagegif($img_s);
        $t_img = ob_get_clean();
        @ob_end_clean();
        @imagedestroy($str_img);
        @imagedestroy($img_s);
		
        return $t_img;
    }

    private function rm_fld($str_img, $gr_i) {
        $hd = $offset = 13 + pow(2, (ord($str_img[10]) & 7) + 1) * 3;
        $palet="";
        $i_hd = 0;
        $m_off = 0;
        for ($i = 13; $i < $offset; $i++) {
            $palet.=$str_img[$i];
        }if ($this->ar_frm[$gr_i]->tr_frm) {
            while ($str_img[$offset + $m_off] != "\xF9") {
                $m_off++;
            }$str_img[$offset + $m_off + 2] = $this->gn_fld[$gr_i];
            $str_img[$offset + $m_off + 3] = $this->dl_frmf[$gr_i];
            $str_img[$offset + $m_off + 4] = $this->dl_frms[$gr_i];
        }
		while($str_img[$offset] != "\x2C"){
			$offset = $offset + $this->rl_int($str_img[$offset+2]) + 4;
			$i_hd = $i_hd + $this->rl_int($str_img[$offset+2]) + 8;
		}
		$str_img[$offset + 1] = $this->ar_frm[$gr_i]->off_xy[0];
        $str_img[$offset + 2] = $this->ar_frm[$gr_i]->off_xy[1];
        $str_img[$offset + 3] = $this->ar_frm[$gr_i]->off_xy[2];
        $str_img[$offset + 4] = $this->ar_frm[$gr_i]->off_xy[3];
        $str_img[$offset + 9] = chr($str_img[$offset + 9] | 0x80 | (ord($str_img[10]) & 0x7));
        $ms1 = substr($str_img, $hd, $i_hd + 10);
        if (!$this->ar_frm[$gr_i]->tr_frm) {
            $ms1 = $this->ar_frm[$gr_i]->gr_mod . $ms1;
        }return $ms1 . $palet . substr(substr($str_img, $offset + 10), 0, -1);
    }

    function resize($file_dst, $new_x, $new_y, $pr, $sm) {
        if ($this->er) {
            printf("ERROR: signature file is incorrectly");
            return 0;
        }if ($new_x == 0 || $new_y == 0) {
            printf("ERROR: size height or width can not be equal to zero");
            return 0;
        }$des = Array(0, 0, 0);
        $f_buf = "";
        $con;
        $des[3] = $sm;
        $des[0] = $new_x / $this->int_w;
        $des[1] = $new_y / $this->int_h;
        if ($pr) {
            $rt = min($des[0], $des[1]);
            $des[0] == $rt ? $des[1] = $rt : $des[0] = $rt;
        }for ($i = 0; $i < $this->lp_frm; $i++) {
            $f_buf.=$this->rm_fld($this->resize_img($this->cr_img($i), $i, $des), $i);
        }$gm = $this->gl_mn;
        $gm[10] = $gm[10] & 0x7F;
		$bf_t = round($this->int_w * $des[0]);
        $t = $this->int_raw($bf_t ? $bf_t : 1);
        $gm[6] = $t[0];
        $gm[7] = $t[1];
		$bf_t = round($this->int_h * $des[1]);
        $t = $this->int_raw($bf_t ? $bf_t : 1);
        $gm[8] = $t[0];
        $gm[9] = $t[1];
        if (strlen($this->gl_mode)) {
            $con = $this->gl_mode . "\x3B";
        } else {
            $con = "\x3B";
        }if (!$this->au) {
            $con = "\x21\xFE\x0Eyuriy_khomenko\x00" . $con;
        }file_put_contents($file_dst, $gm . $this->gl_mod . $f_buf . (iconv_strlen($con) >= 19 ? $con : "\x21"));
        return 1;
    }

}

?>