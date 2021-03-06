<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Adspace;
use Hifone\Models\Carousel;
use Hifone\Models\Link;
use Hifone\Models\Location;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Hifone\Models\SubNode;
use Hifone\Models\Tag;
use Hifone\Models\TagType;
use Illuminate\Support\Facades\Request;

class ApiController extends Controller
{
    //
    public function postUpdateLinkOrder()
    {
        $linkData = Request::get('ids');

        foreach ($linkData as $order => $linkId) {
            // Ordering should be 1-based, data comes in 0-based
            Link::find($linkId)->update(['order' => $order + 1]);
        }

        return $linkData;
    }

    public function postUpdateSectionOrder()
    {
        $sectionData = Request::get('ids');

        foreach ($sectionData as $order => $sectionId) {
            // Ordering should be 1-based, data comes in 0-based
            Section::find($sectionId)->update(['order' => $order + 1]);
        }

        return $sectionData;
    }

    public function postUpdateNodeOrder()
    {
        $nodeData = Request::get('ids');

        foreach ($nodeData as $order => $nodeId) {
            // Ordering should be 1-based, data comes in 0-based
            Node::find($nodeId)->update(['order' => $order + 1]);
        }

        return $nodeData;
    }

    public function postUpdateSubNodeOrder()
    {
        $subNodeData = Request::get('ids');

        foreach ($subNodeData as $order => $subNodeId) {
            // Ordering should be 1-based, data comes in 0-based
            SubNode::find($subNodeId)->update(['order' => $order + 1]);
        }

        return $subNodeData;
    }

    public function postUpdateAdspaceOrder()
    {
        $adspaceData = Request::get('ids');

        foreach ($adspaceData as $order => $adspaceId) {
            // Ordering should be 1-based, data comes in 0-based
            Adspace::find($adspaceId)->update(['order' => $order + 1]);
        }

        return $adspaceData;
    }

    public function postUpdateLocationOrder()
    {
        $locationData = Request::get('ids');

        foreach ($locationData as $order => $locationId) {
            Location::find($locationId)->update(['order' => $order + 1]);
        }

        return $locationData;
    }

    public function postUpdateCarouselOrder()
    {
        $carouselData = Request::get('ids');

        foreach ($carouselData as $order => $carouselId) {
            Carousel::find($carouselId)->update(['order' => $order + 1]);
        }

        return $carouselData;
    }

    public function postUpdateTagOrder()
    {
        $tagData = Request::get('ids');

        foreach ($tagData as $order => $tagId) {
            Tag::find($tagId)->update(['order' => $order + 1]);
        }
    }

    public function postUpdateTagTypeOrder()
    {
        $tagTypeData = Request::get('ids');

        foreach ($tagTypeData as $order => $tagTypeId) {
            TagType::find($tagTypeId)->update(['order' => $order + 1]);
        }
    }


}
