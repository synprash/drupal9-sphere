<?php

namespace Drupal\assignment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the update title API.
 */
class UpdateNodeTitleApiController extends ControllerBase
{


    /**
     * Updates the title based on the provided nid.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *   The request object.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *   The JSON response.
     */
    public function updateNodeTitle(Request $request)
    {
        // Check if the security token is provided in the request header.
        $securityToken = $request->headers->get('X-Security-Token');

        // Validate the security token.
        if ($this->validateSecurityToken($securityToken)) {
            $content = json_decode($request->getContent(), true);

            // Get the nid and title from the request body.
            $nid   = $content['nid'] ?? null;
            $title = ($content['title'] ?? '');

            if (!empty($nid) && !empty($title)) {
                // Load the node by nid.
                $node = \Drupal\node\Entity\Node::load($nid);

                if ($node instanceof \Drupal\node\NodeInterface) {
                    // Set the new title.
                    $node->setTitle($title);
                    $node->save();

                    return new JsonResponse(['status' => 'success', 'message' => 'Title updated successfully.']);
                }
            }

            return new JsonResponse(['status' => 'error', 'message' => 'Failed to update title.']);
        }//end if

        return new JsonResponse(['status' => 'error', 'message' => 'Invalid security token.'], Response::HTTP_UNAUTHORIZED);

    }//end updateNodeTitle()


    /**
     * Validates the provided security token.
     *
     * @param string $token
     *   The security token.
     *
     * @return boolean
     *   TRUE if the token is valid, FALSE otherwise.
     */
    private function validateSecurityToken($token)
    {
        // @todo Create secure tokens from User logins and use here.
        $storedToken = '7654fb1982f04f17a156d75b4aaf9430';
        // Replace with your actual stored token.
        return $token === $storedToken;

    }//end validateSecurityToken()


}//end class
