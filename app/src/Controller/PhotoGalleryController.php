<?php

namespace Doggo\Controller;

use Doggo\Model\PhotoGallery;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ErrorPage;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Upload;
use SilverStripe\Security\Security;
use SilverStripe\Security\Group;
use SilverStripe\Assets\Storage\AssetStore;
use  SilverStripe\Core\Convert;

class PhotoGalleryController extends Controller
{
    private static $allowed_actions = [
        'store',
    ];

    public function store(HTTPRequest $request)
    {
        if (!$request->isPOST()) {
            return $this->httpError(403);
        }
        if (!$this->canAttachExisting()) {
            return $this->httpError(403);
        }
        // Retrieve file attributes required by front end
        $return = array();
        $files = File::get()->byIDs($request->postVar('ids'));
        foreach ($files as $file) {
            $return[] = $this->encodeFileAttributes($file);
        }
        $response = new HTTPResponse(Convert::raw2json($return));
        $response->addHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param $data
     * @param int $status
     * @param bool $forceObject
     * @return HTTPResponse
     */
    /*public function json($data, $status = 200, $forceObject = false)
    {
        $flags = null;

        if ($forceObject) {
            $flags = JSON_FORCE_OBJECT;
        }

        $response = (new HTTPResponse())
            ->setStatusCode($status)
            ->setBody(json_encode($data, $flags))
            ->addHeader('Content-Type', 'application/json');

        return $response;
    }*/
}
