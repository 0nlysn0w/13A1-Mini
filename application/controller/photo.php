<?php

class Photo extends Controller
{

    /**
     * PAGE: index
     * This method handles showing the image upload form
     */
    public function index()
    {
        $this->view->render('photo/index');
    }

    /**
     * PAGE: upload
     * This method handles the actual file upload
     */
    public function addPhoto()
    {
        //load the photo model to handle upload
        $photo_model = $this->loadModel('Photo');
        //perform the upload method, put result (true or false) in $upload_succesfull
        $upload_succesfull = $photo_model->uploadPhoto();

        if ($upload_succesfull) {
            
        } else {

        }
    }
}

