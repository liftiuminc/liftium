#!/usr/bin/ruby

require 'rubygems'
require 'json'
require 'sinatra'
require 'sparklines'
require 'ftools';

@@rrd_path       = "/var/lib/ganglia/rrds/"
@@liftium_path   = "#{@@rrd_path}/Liftium/localhost.localdomain/"

# 2x2 pixel gif
@@fallback_image = File.expand_path( File.dirname( $0 ) + '/white.gif' );

def send_rrd_graph(rrd_opts) 
  temp_file =  Tempfile.new('rrd-image')
  rrd_opts.unshift temp_file.path

  # on success, send the graph
  if system "rrdtool graph #{rrd_opts.join(' ')}"
    send_file temp_file.path, 
                :type        => 'image/png',
			    :disposition => nil
    temp_file.close
  # otherwise, send a fallback image  
  else 
    send_file @@fallback_image,
                :type        => 'image/gif',
                :disposition => nil   
  end 
end

get '/chart' do
  rrd_opts = []
  colors = [ "00FF00C0", "FF0000C0", "0000FFC0" ]
  net = params[:net]
  tag = params[:tag]

  rrd_opts << "--start=-#{params[:range]}"
  rrd_opts << "--title=\"#{params[:title]}\""
  rrd_opts << "--vertical-label=requests"
  rrd_opts << "--height=#{params[:height]}"
  rrd_opts << "--width=#{params[:width]}"
  rrd_opts << "--slope-mode"
  rrd_opts << "--lower-limit=0"

  ['attempts','rejects','loads'].each do |stat|
    rrd_opts << "DEF:#{stat}=#{@@liftium_path}/liftium_#{net}_#{tag}_#{stat}.rrd:sum:AVERAGE"
    rrd_opts << "AREA:#{stat}\##{colors.shift}:#{stat}"
  end
  rrd_opts << "VDEF:attempts_max=attempts,MAXIMUM"
  rrd_opts << "CDEF:fill_rate=loads,attempts,/,attempts_max,*"
  rrd_opts << "LINE:fill_rate#000000:fill_rate"

  send_rrd_graph(rrd_opts)
end 

get '/misc' do
  rrd_opts = []
  stat = params[:stat]

  rrd_opts << "--start=\"-#{params[:range]}\""
  rrd_opts << "--title=\"#{params[:title]}\""
  rrd_opts << "--height=#{params[:height]}"
  rrd_opts << "--width=#{params[:width]}"
  rrd_opts << "--slope-mode"

  rrd_opts << "DEF:#{stat}=#{@@liftium_path}/liftium_#{stat}.rrd:sum:AVERAGE"
  rrd_opts << "AREA:#{stat}\#000000CC:#{stat}"

  send_rrd_graph(rrd_opts)
end 


get '/sparkline' do
  results = []
  rrd_opts = []
  net = params[:net]
  tag = params[:tag]
  rrd_opts << "--start=-#{params[:range]}"

  rrd_opts << "-m 75"
  ['loads','attempts'].each do |stat|
    rrd_opts << "DEF:#{stat}=#{@@liftium_path}/liftium_#{net}_#{tag}_#{stat}.rrd:sum:AVERAGE"
  end
  rrd_opts << "CDEF:fill=loads,attempts,/,100,*"
  rrd_opts << "XPORT:fill"

  xml = `rrdtool xport #{rrd_opts.join(' ')}`
  data = xml.scan(/<v>(.*)<\/v>/)
  data.each do |item|
    if item[0].to_s == "NaN"
      results << 0
    else
      # Convert rrds sci notation to decimal
      value = "%4.2f" % item[0]
      results << value.to_i
    end
  end
  image = Sparklines.plot(results, :type => 'area',
                          :min => 0,
                          :max => 100,
			  :step => 3,
			  :upper => 0,
			  :above_color => "#0098E0",
                          :height => params[:height] )
	
  content_type 'image/png'
  image	
end

