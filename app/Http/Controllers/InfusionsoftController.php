<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use Response;

class InfusionsoftController extends Controller
{
    /**
     * @var InfusionsoftHelper
     */
    private $infusionsoftHelper;


    /**
     * InfusionsoftController constructor.
     * @param InfusionsoftHelper $infusionsoftHelper
     */
    public function __construct(InfusionsoftHelper $infusionsoftHelper)
    {
        $this->infusionsoftHelper = $infusionsoftHelper;
    }

    public function authorizeInfusionsoft()
    {
        return $this->infusionsoftHelper->authorize();
    }

    public function testInfusionsoftIntegrationGetEmail($email)
    {
        return Response::json($this->infusionsoftHelper->getContact($email));
    }

    public function testInfusionsoftIntegrationAddTag($contact_id, $tag_id)
    {

        return Response::json($this->infusionsoftHelper->addTag($contact_id, $tag_id));
    }

    public function testInfusionsoftIntegrationGetAllTags()
    {

        return Response::json($this->infusionsoftHelper->getAllTags());
    }

    public function testInfusionsoftIntegrationCreateContact()
    {

        return Response::json($this->infusionsoftHelper->createContact([
            'Email' => uniqid() . '@test.com',
            "_Products" => 'ipa,iea'
        ]));
    }
}
