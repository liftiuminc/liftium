module ChartsHelper

  def tag_sparkline_url(tag, range="1h", width=50, height=25) 
	# http://rgraph.liftium.com/sparkline?net=<%=tag.network_id%>&tag=<%=tag.id%>&range=15m&width=50&height=25
	data = {
		:net => tag.network_id,
		:tag => tag.id,
		:range => range,
		:width => width,
		:height => height,
	}

	"http://rgraph.liftium.com/sparkline?" + data.to_query
  end

  def tag_fillrate_url(tag, range="1h", width=600, height=250) 
      case range[1]
	when "h"
	  time = "Hourly"
	when "d"
	  time = "Daily"
	when "w"
	  time = "Weekly"
	when "m"
	  range = range + "onth"
	  time = "Monthly"
	when "y"
	  time = "Yearly"
	else 
	  time = ""
      end

	data = {
		:net => tag.network_id,
		:tag => tag.id,
		:range => range,
		:title => title = time + " Stats for " + tag.tag_name,
		:width => width,
		:height => height,
	}

	"http://rgraph.liftium.com/chart?" + data.to_query
  end

end
