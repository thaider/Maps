<?php

declare( strict_types = 1 );

namespace Maps\SemanticMW;

use DataValues\Geo\Values\LatLongValue;

class TemplatedPopup {

	private $templateName;
	private $extraParameter;

	public function __construct( string $templateName, string $extraParameter ) {
		$this->templateName = $templateName;
		$this->extraParameter = $extraParameter;
	}

	public function getWikiText( string $title, LatLongValue $latLong, array $properties ): string {
		$segments = [
			$this->templateName,
			'title=' . $title,
			'latitude=' . $latLong->getLatitude(),
			'longitude=' . $latLong->getLongitude(),
			'userparam=' . $this->extraParameter
		];

		foreach ( $properties as $name => $value ) {
			$segments[] = $name . '=' . $value;
			$segments[] = $value;
		}

		return '{{' . implode( '|', $segments ) . '}}';
	}

}