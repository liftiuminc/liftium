use strict;
use warnings;
use Geo::IP;
use Plack::Request;
use Regexp::Common  qw[net];
use File::Basename  qw[dirname];

my $db = dirname( __FILE__ ) . "/GeoIP.dat";
my $GI = Geo::IP->open( $db, GEOIP_MEMORY_CACHE | GEOIP_CHECK_CACHE );

### Nick worries that not all downstream clients/caches respect the
### Cache-control: directive if we respond with HTTP/1.0. Confirmed
### are Varnish & Firefox 3.5 who respect the directive regardless of
### HTTP protocol. 
### The RFC states that HTTP 1.0 caches MAY implement Cache-Control,
### but are not required to do so. See section 14.9 of the RFC:
### http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
### For safety sake, we coerce the response to be HTTP/1.1 so
### cache control is obeyed as we want. Since we control the query &
### response stack, this is safe to do --Jos. See FB 151
{   require Plack::Server::Standalone;
    my $org = Plack::Server::Standalone->can('write_all');

    no warnings 'redefine';
    *Plack::Server::Standalone::write_all = sub {
        my ($self, $sock, $buf, $timeout) = @_;
        
        $buf =~ s|HTTP/1.0|HTTP/1.1|;
        
        return $org->( $self, $sock, $buf, $timeout );
    };
}    

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


