#!/usr/bin/perl

use utf8;
use JSON::XS;
use Data::Dump qw(dump);

my $json_string = "";

$data  = decode_json $json_string;

print $data->{1}->{order_keyword};
print "\n";

=BEGIN
use JSON;
use Data::Dump qw(dump);
my $data = from_json( $json_string );
use JSON::XS;

$json_data="{ \"hello\":\"Hello, \u00ab\u043f\u0440\u0438\u0432\u0435\u0442\u00bb\u2116 \u263a. Good by.\" }";

my $json_xs = JSON::XS->new();
		$json_xs->utf8(1);
		$json_xs->decode($json_data);

print dump($json_xs);
print "\n";
 
#use utf8;
use Unicode::MapUTF8 qw(to_utf8 from_utf8);
 
print ("Как Вас зовут? ");
$name= <STDIN>;
 
chomp ($name);
 
#utf8::encode($name);
my $output = to_utf8({ -string => $name, -charset => 'utf8' });
 
print "$output\n";

{
	\"1\": {
		\"order_name\": \"test\\u00ab\\u043f\\u0440\\u0438\\u0432\\u0435\\u0442\\u00bb \\u2116 \\u263a\",
		\"order_time\": \"15:07:2010-06:01:2012\",
		\"order_keyword\": \"brandspotter\"
	},
	\"2\": {
		\"count_posts\": 56,
		\"uniq_auth\": 32,
		\"post_in_day\": 0,
		\"count_hosts\": 34,
		\"engage\": 32,
		\"audience\": 22685,
		\"period\": \"15:07:2010-06:01:2012\"
	}
}
=END