<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\map_authors_to_quotes\Controller;

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

class MapAuthorsToQuotesController {
  public function content() {
	$markup = "";

	// Switch to external database
	\Drupal\Core\Database\Database::setActiveConnection('gtt6');

	// Get a connection going
	$db = \Drupal\Core\Database\Database::getConnection();

	$query = $db->select('content_type_quote', 'ctq');
	$query->fields('ctq', array('nid', 'field_author_nid'));
	//$query->range(1,10);
	$quotes = $query->execute()->fetchAll();

$coutn = 0;
	// Switch back
	\Drupal\Core\Database\Database::setActiveConnection();
	foreach($quotes as $quote){
$node = \Drupal::entityTypeManager()->getStorage('node')->load($quote->nid);

	       // $node = \Drupal\Core\Entity\Entity::load(723798);//$quote->nid);
        	$node->field_author->target_id = $quote->field_author_nid;
        	$node->save();
       // 	$markup .= 'Updated quote ' . $quote->nid . '<br />';
$count++;
	}
    return array(
      '#type' => 'markup',
      '#markup' => t($count),
    );
  }
}
