use LWP::UserAgent;
use HTML::ContentExtractor;

my $extractor = HTML::ContentExtractor->new();
my $agent=LWP::UserAgent->new;

#my $url='http://twitter.com/nano_rus/status/157613058171224065';
my $url=$ARGV[0];
#print $ARGV[0];
my $res=$agent->get($url);
my $HTML = $res->decoded_content();

$extractor->extract($url,$HTML);
# print $extractor->as_html();
print $extractor->as_text();
