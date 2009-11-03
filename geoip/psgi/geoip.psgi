use strict;
use warnings;
use Geo::IP;
use Plack::Request;
use Regexp::Common  qw[net];
use File::Basename  qw[dirname];

my $db = dirname( __FILE__ ) . "/GeoIP.dat";
my $GI = Geo::IP->open( $db, GEOIP_MEMORY_CACHE | GEOIP_CHECK_CACHE );

my $app = sub {
    my $env = shift;
    my $req = Plack::Request->new( $env );
    my $ip  = $req->uri->query                              ||  # specific IP
              $req->headers->header( 'X-Forwarded-For' )    ||  # from a proxy
              $req->address;                                    # client

    return [ 400, [ 'X-GeoIP-Error' => "Invalid IP: $ip" ], [] ]
        unless ( $ip and $ip =~ /$RE{net}{IPv4}/ );

    ### normalize to /24 -- actually more work, so disable for now
    #$ip =~ s/\.\d+$/.0/;
    
    my $c = $GI->country_code_by_addr( $ip ) || '';
    
    ### Liftium.geo = can be removed once the config.php can do this 
    ### call again --Jos
    return [ 200, [], [ qq[Liftium.geo={"country":"$c"}] ] ];
};
