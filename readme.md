# nzpMedia class

Parses links to popular media websites.

## Quick 'n' dirty 'xample

    <?php
    import("nzpMedia.php");
    
    // Creating nzpMediaObj object
    $link = "http://youtu.be/OBh_W7s59EA";
    $video = nzpMedia::getInfo($link);
    
    // Get api data
    nzpMedia::addApiKey("youtube", "MY_YOUTUBE_API_KEY");
    $api_data = nzpMedia::getApiData($video);
    ?>