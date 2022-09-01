<?php

namespace Doggo\Model;

use SilverStripe\ORM\DataObject;

class PhotoGallery extends DataObject
{
    private static $table_name = 'PhotoGallery';

    private static $db = [
        'Title' => 'Varchar',
        'imgURL' => 'Varchar',
        'ProviderCode' => 'Varchar(100)',
        'IsApproved' => 'Boolean',
    ];

    private static $summary_fields = [
        'Title' => 'Title',
    ];

}
  