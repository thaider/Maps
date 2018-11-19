<?php

namespace Maps;

use Maps\SemanticMW\ResultPrinters\KmlPrinter;
use Maps\SemanticMW\ResultPrinters\MapPrinter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SemanticMaps {

	private $mwGlobals;

	private function __construct( array &$mwGlobals ) {
		$this->mwGlobals =& $mwGlobals;
	}

	public static function newFromMediaWikiGlobals( array &$mwGlobals ) {
		return new self( $mwGlobals );
	}

	public function initExtension() {
		// Hook for initializing the Geographical Data types.
		$this->mwGlobals['wgHooks']['SMW::DataType::initTypes'][] = 'Maps\MediaWiki\SemanticMapsHooks::initGeoDataTypes';

		// Hook for defining the default query printer for queries that ask for geographical coordinates.
		$this->mwGlobals['wgHooks']['SMWResultFormat'][] = 'Maps\MediaWiki\SemanticMapsHooks::addGeoCoordsDefaultFormat';

		// Hook for adding a Semantic Maps links to the Admin Links extension.
		$this->mwGlobals['wgHooks']['AdminLinks'][] = 'Maps\MediaWiki\SemanticMapsHooks::addToAdminLinks';

		$this->registerResourceModules();

		$this->registerGoogleMaps();
		$this->registerLeaflet();
		$this->registerOpenLayers();

		$this->mwGlobals['smwgResultFormats']['kml'] = KmlPrinter::class;

		$this->mwGlobals['smwgResultAliases'][$this->mwGlobals['egMapsDefaultServices']['qp']][] = 'map';
		MapPrinter::registerDefaultService( $this->mwGlobals['egMapsDefaultServices']['qp'] );

		// Internationalization
		$this->mwGlobals['wgMessagesDirs']['SemanticMaps'] = __DIR__ . '/i18n';
	}

	private function registerResourceModules() {
		$moduleTemplate = [
			'position' => 'bottom',
			'group' => 'ext.semanticmaps',
		];

		$this->mwGlobals['wgResourceModules']['ext.sm.common'] = $moduleTemplate + [
				'localBasePath' => __DIR__ . '/../resources',
				'remoteExtPath' => 'Maps/resources',
				'scripts' => [
					'ext.sm.common.js'
				]
			];
	}

	private function registerGoogleMaps() {
		$this->mwGlobals['wgResourceModules']['ext.sm.googlemaps3ajax'] = [
			'localBasePath' => __DIR__ . '/../resources/GoogleMaps',
			'remoteExtPath' => 'Maps/resources/GoogleMaps',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.googlemaps3',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.googlemaps3ajax.js'
			]
		];

		/* @var MappingService $googleMaps */
		$googleMaps = MappingServices::getServiceInstance( 'googlemaps3' );
		$googleMaps->addResourceModules( [ 'ext.sm.googlemaps3ajax' ] );

		MapPrinter::registerService( $googleMaps );

		$this->mwGlobals['smwgResultFormats'][$googleMaps->getName()] = MapPrinter::class;
		$this->mwGlobals['smwgResultAliases'][$googleMaps->getName()] = $googleMaps->getAliases();
	}

	private function registerLeaflet() {
		$this->mwGlobals['wgResourceModules']['ext.sm.fi.leafletajax'] = [
			'localBasePath' => __DIR__ . '/../resources/leaflet',
			'remoteExtPath' => 'Maps/resources/leaflet',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.leaflet',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.leafletajax.js'
			]
		];

		/* @var MappingService $leaflet */
		$leaflet = MappingServices::getServiceInstance( 'leaflet' );
		$leaflet->addResourceModules( [ 'ext.sm.fi.leafletajax' ] );

		MapPrinter::registerService( $leaflet );

		$this->mwGlobals['smwgResultFormats'][$leaflet->getName()] = MapPrinter::class;
		$this->mwGlobals['smwgResultAliases'][$leaflet->getName()] = $leaflet->getAliases();
	}

	private function registerOpenLayers() {
		$this->mwGlobals['wgResourceModules']['ext.sm.fi.openlayersajax'] = [
			'localBasePath' => __DIR__ . '/../resources/OpenLayers',
			'remoteExtPath' => 'Maps/resources/OpenLayers',
			'group' => 'ext.semanticmaps',
			'dependencies' => [
				'ext.maps.openlayers',
				'ext.sm.common'
			],
			'scripts' => [
				'ext.sm.openlayersajax.js'
			]
		];

		/* @var MappingService $openLayers */
		$openLayers = MappingServices::getServiceInstance( 'openlayers' );
		$openLayers->addResourceModules( [ 'ext.sm.fi.openlayersajax' ] );

		MapPrinter::registerService( $openLayers );

		$this->mwGlobals['smwgResultFormats'][$openLayers->getName()] = MapPrinter::class;
		$this->mwGlobals['smwgResultAliases'][$openLayers->getName()] = $openLayers->getAliases();
	}

}
