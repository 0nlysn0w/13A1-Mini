<?php

class PhotosModel
{

    //This method is not working yet. But you will need to write it.
    public function getAllPhotos()
    {
        $sql = "SELECT * FROM photos";
        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function uploadPhoto()
    {
        $target_file = UPLOAD_PATH . basename($_FILES["fileToUpload"]["name"]);

        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check == false) {
                //File is not an image, return false (todo: return an error message)
                $_SESSION['feedback_negative'][] = UPLOAD_FILE_NOT_AN_IMAGE;
                return false;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            //File already exists, return false (todo: return an error message)
            $_SESSION['feedback_negative'][] = UPLOAD_FILE_ALREADY_EXISTS;
            return false;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            //File is too big, return false (todo: return an error message)
            $_SESSION['feedback_negative'][] = UPLOAD_FILE_SIZE_TOO_BIG;
            return false;
        }
        // Allow certain file formats
        $imageFileType = strtolower($imageFileType);

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            //File doesnt have proper extension, return false (todo: return an error message)
            $_SESSION['feedback_negative'][] = UPLOAD_FILE_WRONG_EXTENSION; 
            return false;
        }

        $latlng = $this->getLonLat($_FILES["fileToUpload"]["tmp_name"]);

        if ($latlng == false) {
            $_SESSION['feedback_negative'][] = UPLOAD_FILE_NO_GEO_DATA;
            return false;
        }
        
        if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            //File wasnt uploaded, return false (todo: return an error message)
            $_SESSION['feedback_negative'][] = UPLOAD_FILE_FAILED;
            return false;
        }

        $sql = "";
        $query = $this->db->prepare($sql);
        $query->execute(array(':photo_location' => $target_file,
                              ':latitude' => $latlng['lat'],
                              ':longitude' => $latlng['lng']));
        $count = $query->rowCount();
        if ($count == 1) {
            $_SESSION['feedback_negative'][] = $latlng['lat'];
            $_SESSION['feedback_negative'][] = $latlng['lng'];
            return true;
        } else {
            $_SESSION['feedback_negative'][] = UPLOAD_FILE_FAILED;
            unlink($target_file);
            return false;
        }
        
    }

    private function getLonLat($image)
    {
        $exif = exif_read_data($image);

        if(array_key_exists('GPSLongitude', $exif) && array_key_exists('GPSLatitude', $exif)) {
            $lng = $this->getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
            $lat = $this->getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
            $latlng = array("lat" => $lat,
                              "lng" => $lng);
            return $latlng;
        }
        else {
            return false;
        }
    }

    private function getGps($exifCoord, $hemi) {
        $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }
 
    private function gps2Num($coordPart) {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0)
            return 0;
        if (count($parts) == 1)
            return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
}
