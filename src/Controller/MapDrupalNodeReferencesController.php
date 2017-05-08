<?php
/**
 * @file
 * Contains \Drupal\map_drupal_node_references\Controller\MapDrupalNodeReferencesController.
 */
namespace Drupal\map_drupal_node_references\Controller;

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

class MapDrupalNodeReferencesController {
	
	public function index() {
		
		 return array(
			'#markup' => '<p><a href="/map-drupal-node-references/authors-to-quotes">' . t('Map Authors to Quotes') . '</a></p>',
		);
	}
	// TODO: Make this accept start and end counts to proccess.
	public function authorsToQuotes($limit, $startNode) {

		// Switch to external database
		\Drupal\Core\Database\Database::setActiveConnection('gtt6');

		// Get a connection going
		$db = \Drupal\Core\Database\Database::getConnection();

		$query = $db->select('content_type_quote', 'ctq');
		$query->fields('ctq', array('nid', 'field_author_nid'));
		$query->orderBy('nid');
		$query->range(0, $limit);
		
		// Check the file to see what node to start on
		$finder = new Finder();
		$finder->in('/var/www/greatthoughtstreasury.com/config/')->files()->name('quote_id.txt');
		
		// Get the last file id.
		foreach($finder as $file) {
			
			$quote_id = $file->getContents();
			
			// Set the start node to this id
			$startNode = $quote_id;
			
			break;
		}

		// If start node is given, then only grab quotes from that point forward.
		if($startNode > 0)
			$query->condition('nid', $startNode, '>=');

		$quotes = $query->execute()->fetchAll();

		// Switch back
		\Drupal\Core\Database\Database::setActiveConnection();
		foreach($quotes as $quote){
			$node = \Drupal::entityTypeManager()->getStorage('node')->load($quote->nid);
			$node->field_author->target_id = $quote->field_author_nid;
			$node->save();
		}

		// Set the next author id.
		foreach($finder as $file) {
			
			$quote_id = $file->openFile('w')->fwrite($quote->nid);
			
			break;
		}
		
		// Worked for 10 minutes.
		
		return array(
			'#type' => 'markup',
			'#markup' => t('Ended on ' . $quote->nid),
		);
		
		
	}
}
