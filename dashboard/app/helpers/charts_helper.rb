module ChartsHelper

  def tag_sparkline_url(tag, range="1h", width=50, height=25) 
  end

  def tag_fillrate_url(tag, range="1h", width=600, height=250) 
	case range
	  when "1h"
	    time = "Hourly"
	  when "1d"
	    time = "Daily"
	  when "1w"
	    time = "Weekly"
	  when "1m"
	    range = "1month"
	    time = "Monthly"
	  when "1y"
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
