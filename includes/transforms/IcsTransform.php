<?php

class IcsTransform {

    const GITHUB_REPO = [
        //the following constants should have been defined in config.php
        "User" => GITHUB_REPO_USER,
        "Name" => GITHUB_REPO_NAME
    ];

    public static function ObjectToIcs( object $ResponseObj, object $GithubRelease ) {
        $publishDate = $GithubRelease->published_at;
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "PRODID:-//" . GITHUB_REPO_USER . "//" . GITHUB_REPO_NAME ." " . $GithubRelease->tag_name . "//EN\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-MS-OLK-FORCEINSPECTOROPEN:FALSE\r\n";
        $ical .= "X-WR-CALNAME: " . CALENDAR_NAME . "\r\n";
        $ical .= "X-WR-TIMEZONE:" . CALENDAR_DEFAULT_TIMEZONE . "\r\n"; //perhaps allow this to be set through a GET or POST parameter?
        $ical .= "X-PUBLISHED-TTL:PT1D\r\n";
        foreach( $ResponseObj->Cal as $EventKey => $Event ){

            $description = $Event->description;

            //The following tags should be supported in the html description:
            //<P DIR=LTR></P>
            //<SPAN LANG=en-us></SPAN>
            //<FONT FACE="Calibri" COLOR="#123ABC"></FONT>
            //<BR>
            //<PRE></PRE>
            //<HEADER></HEADER>
            //<B></B>
            //<I></I>
            $htmlDescription = "<P DIR=LTR>" . $Event->description . "</P>";

            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "CLASS:PUBLIC\r\n";
            $ical .= "DTSTART;VALUE=DATE:" . $Event->date->format( 'Ymd' ) . "\r\n";// . "T" . $Event->date->format( 'His' ) . "Z\r\n";
            //$Event->date->add( new DateInterval( 'P1D' ) );
            //$ical .= "DTEND:" . $Event->date->format( 'Ymd' ) . "T" . $Event->date->format( 'His' ) . "Z\r\n";
            $ical .= "DTSTAMP:" . date( 'Ymd' ) . "T" . date( 'His' ) . "Z\r\n";
            /** The event created in the calendar is specific to this year, next year it may be different.
             *  So UID must take into account the year
             *  Next year's event should not cancel this year's event, they are different events
             **/
            $ical .= "UID:" . md5( GITHUB_REPO_NAME . "-" . $EventKey . '-' . $Event->date->format( 'Y' ) ) . "\r\n";
            $ical .= "CREATED:" . str_replace( ':' , '', str_replace( '-', '', $publishDate ) ) . "\r\n";
            $desc = "DESCRIPTION:" . str_replace( ',','\,', $description );
            $ical .= strlen( $desc ) > 75 ? rtrim( chunk_split( $desc,71,"\r\n\t" ) ) . "\r\n" : "$desc\r\n";
            $ical .= "LAST-MODIFIED:" . str_replace( ':' , '', str_replace( '-', '', $publishDate ) ) . "\r\n";
            $summaryLang = ";LANGUAGE=" . CALENDAR_DEFAULT_LANGUAGE;
            $summary = "SUMMARY".$summaryLang.":" . str_replace( ',','\,',str_replace( "\r\n"," ", $Event->name ) );
            $ical .= strlen( $summary ) > 75 ? rtrim( chunk_split( $summary,75,"\r\n\t" ) ) . "\r\n" : $summary . "\r\n";
            $ical .= "TRANSP:TRANSPARENT\r\n";
            $ical .= "X-MICROSOFT-CDO-ALLDAYEVENT:TRUE\r\n";
            $ical .= "X-MICROSOFT-DISALLOW-COUNTER:TRUE\r\n";
            $xAltDesc = 'X-ALT-DESC;FMTTYPE=text/html:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">\n<HTML>\n<BODY>\n\n';
            $xAltDesc .= str_replace( ',','\,', $htmlDescription );
            $xAltDesc .= '\n\n</BODY>\n</HTML>';
            $ical .= strlen( $xAltDesc ) > 75 ? rtrim( chunk_split( $xAltDesc,71,"\r\n\t" ) ) . "\r\n" : "$xAltDesc\r\n";
            $ical .= "END:VEVENT\r\n";
        }
        $ical .= "END:VCALENDAR";
        return $ical;
    }

    public static function getGithubReleaseInfo() {
        [ "User" => $repoUser, "Name" => $repoName ] = self::GITHUB_REPO;
        $returnObj = new stdClass();
        $GithubReleasesAPI = "https://api.github.com/repos/{$repoUser}/{$repoName}/releases/latest";
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $GithubReleasesAPI );
        curl_setopt( $ch, CURLOPT_USERAGENT, $repoName );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $currentVersionForDownload = curl_exec( $ch );

        if ( curl_errno( $ch ) ) {
          $returnObj->status = "error";
          $returnObj->message = curl_error( $ch );
        }
        curl_close( $ch );

        $GitHubReleasesObj = json_decode( $currentVersionForDownload );
        if( json_last_error() !== JSON_ERROR_NONE ){
            $returnObj->status = "error";
            $returnObj->message = json_last_error_msg();
        } else {
            $returnObj->status = "success";
            $returnObj->obj = $GitHubReleasesObj;
        }
        return $returnObj;
    }
}
