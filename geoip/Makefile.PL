use ExtUtils::MakeMaker;
use strict;

WriteMakefile (
    NAME            => 'Liftium::GeoIP',
    VERSION         => 1,
    dist            => { COMPRESS => 'gzip -9f', SUFFIX => 'gz' },
    PREREQ_PM       => { 'Plack'                => '0.9008',
                         'Plack::Request'       => 0,
                         'Geo::IP'              => '1.38',
                         'Regexp::Common'       => 0,
                         'Parallel::Prefork'    => 0,   # for standalone::prefork
                    },
);
