<?php

namespace JohnRDOrazio\SimpleAPI\Transforms;

class XmlTransform {

    public static function Array2XML(array $data, ?\SimpleXMLElement &$xml) : void {
        foreach( $data as $key => $value ) {
            // if the key is a number, it needs text with it to actually work
            if( is_numeric( $key ) ) {
                $key = "numeric_$key";
            }
            if( is_array( $value ) ) {
                $new_object = $xml->addChild( $key );
                self::Array2XML( $value, $new_object );
            } else {
                $xml->addChild( $key, $value );
            }
        }
    }

    public static function ObjectToXml( object $object ) : string|bool {
        //The following constants should have been defined in config.php
        $XMLParentElement   = XML_PARENT_ELEMENT;
        $XMLNamespace       = XML_NAMESPACE;
        //First we need to convert the object to an associative array
        $jsonStr = json_encode( $object );
        $jsonObj = json_decode( $jsonStr, true );
        //Now we can transform the array to XML
        $xml = new \SimpleXMLElement ( "<?xml version=\"1.0\" encoding=\"UTF-8\"?" . "><{$XMLParentElement} xmlns=\"{$XMLNamespace}\"/>" );
        XmlTransform::Array2XML( $jsonObj, $xml );
        return $xml->asXML();
    }

}
