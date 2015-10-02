<?php

class GSheet_Parser {

	private $data;

    public function __construct() {
		$this->parser = xml_parser_create( 'UTF-8' );

		xml_set_object( $this->parser, $this );
		xml_set_element_handler( $this->parser, 'parse_element_start', 'parse_element_end' );
		xml_set_character_data_handler( $this->parser, 'parse_data' );
	}

	public function parse( $source ) {
		$this->data = array();
		$this->block = null;
		$this->key = null;

		xml_parse( $this->parser, file_get_contents( $source ), TRUE );
		return $this->data;
	}

	public function parse_element_start( $parser, $name, array $attributes ) {
		if ( $name == "ENTRY" ) {
			$this->block = array();
		} else if ( $this->block !== null ) {
			$this->key = $name;
		}
	}

	public function parse_element_end( $parser, $name ) {
		if ( $name == "ENTRY" ) {
			$this->data[] = $this->block;
			$this->block = null;
		}
	}

	public function parse_data( $parser, $data ) {
		if ( $this->block !== null && substr( $this->key, 0, 4 ) === "GSX:" ) {
			$key = strtolower( substr( $this->key, 4 ) );
			$this->block[ $key ] = $data;
			$this->key = null;
		}
	}

}
