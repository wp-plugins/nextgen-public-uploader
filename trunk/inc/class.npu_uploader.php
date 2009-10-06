<?php
require_once (NGGALLERY_ABSPATH."/admin/functions.php");

class UploaderNggAdmin extends nggAdmin
{
    public $arrImageIds = false;
    public $strGalleryPath = false;
    public $arrImageNames = false;
    public $strFileName = false;
    public $blnRedirectPage = false;
    public $arrThumbReturn = false;
    public $arrEXIF = false;

    function upload_images() {
    // upload of pictures
        
        global $wpdb;

        // Images must be an array
        $imageslist = array();

        // get selected gallery
        $galleryID = (int) $_POST['galleryselect'];

        if ($galleryID == 0) {
            nggGallery::show_error(__('No gallery selected !','nggallery'));
            return;
        }

        // get the path to the gallery
        $gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->nggallery WHERE gid = '$galleryID' ");
        
        if (!$gallerypath){
            nggGallery::show_error(__('Failure in database, no gallery path set !','nggallery'));
            return;
        }

        // read list of images
        $dirlist = $this->scandir(WINABSPATH.$gallerypath);

        foreach ($_FILES as $key => $value) {

            // look only for uploded files
            if ($_FILES[$key]['error'] == 0) {
                $temp_file = $_FILES[$key]['tmp_name'];
                $filepart = pathinfo ( strtolower($_FILES[$key]['name']) );
                // required until PHP 5.2.0
                $filepart['filename'] = substr($filepart["basename"],0 ,strlen($filepart["basename"]) - (strlen($filepart["extension"]) + 1) );

                $filename = sanitize_title($filepart['filename']) . '.' . $filepart['extension'];

                // check for allowed extension and if it's an image file
                $ext = array('jpeg', 'jpg', 'png', 'gif');
                if ( !in_array($filepart['extension'], $ext) || !@getimagesize($temp_file) ){
                    nggGallery::show_error('<strong>'.$_FILES[$key]['name'].' </strong>'.__('is no valid image file!','nggallery'));
                    continue;
                }

                // check if this filename already exist in the folder
                $i = 0;
                while (in_array($filename,$dirlist)) {
                    $filename = sanitize_title($filepart['filename']) . '_' . $i++ . '.' .$filepart['extension'];
                }

                $dest_file = WINABSPATH . $gallerypath . '/' . $filename;

                //check for folder permission
                if (!is_writeable(WINABSPATH.$gallerypath)) {
                    $message = sprintf(__('Unable to write to directory %s. Is this directory writable by the server?', 'nggallery'), WINABSPATH.$gallerypath);
                    nggGallery::show_error($message);
                    return;
                }

                // save temp file to gallery
                if (!@move_uploaded_file($_FILES[$key]['tmp_name'], $dest_file)){
                    nggGallery::show_error(__('Error, the file could not moved to : ','nggallery').$dest_file);
                    $this->check_safemode(WINABSPATH.$gallerypath);
                    continue;
                }
                if (!$this->chmod ($dest_file)) {
                    nggGallery::show_error(__('Error, the file permissions could not set','nggallery'));
                    continue;
                }

                // add to imagelist & dirlist
                $imageslist[] = $filename;
                $dirlist[] = $filename;

            }
        }

        if (count($imageslist) > 0) {

            // add images to database
            $image_ids = $this->add_Images($galleryID, $imageslist);
            $this->arrThumbReturn = array();
            foreach ($image_ids as $pid) {
                $wpdb->query("UPDATE $wpdb->nggpictures SET exclude = 1 WHERE pid = '$pid'");
                $this->arrThumbReturn[] = $this->create_thumbnail($pid);
            }
            $this->arrImageIds = array();
            $this->arrImageIds = $image_ids;
            $this->arrImageNames =array();
            $this->arrImageNames = $imageslist;
            $this->strGalleryPath = $gallerypath;
        }

        return;

    } // end function

}
?>
