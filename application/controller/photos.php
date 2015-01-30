<?php

class Photos extends Controller
{

    /**
     * PAGE: index
     * This method handles showing the image upload form
     */
    public function index()
    {
        //In index we show ALL the pictures uploaded, with a link to the upload form.
        $this->view->render('photos/index');
    }

    /**
     * PAGE: upload
     * This method handles the actual file upload
     */

    public function addPhoto()
    {
        //In addphoto we show the upload form.
        $this->view->render('photos/addphoto');
    }

    public function addPhoto_action()
    {
        //load the photo model to handle upload
        $photos_model = $this->loadModel('Photos');
        //perform the upload method, put result (true or false) in $upload_succesfull
        $upload_succesfull = $photos_model->uploadPhoto();

        if ($upload_succesfull) {
            header('location: ' . URL . 'photos/index');
        } else {
            header('location: ' . URL . 'photos/addphoto');
        }
    }
}

