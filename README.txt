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