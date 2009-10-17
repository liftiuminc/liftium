class DataExportController < ApplicationController
  def index
    case params[:interval]
      when "day" 
        @fill_stats = FillsDay.find(:all, :order => "day")
        @time_name = "Day"
      when "hour" 
        @fill_stats = FillsHour.find(:all, :order => "hour")
        @time_name = "Hour"
      else 
        @fill_stats = FillsMinute.find(:all, :order => "minute")
        @time_name = "Minute"
    end

    if params[:csv]
      self.export_to_csv(@time_name)
    end
  end

  def export_to_csv(time_name)
    require 'fastercsv'

    @outfile = "fills_" + time_name + "_" + Time.now.strftime("%m-%d-%Y") + ".csv"
    
    total_attempts = 0
    total_loads = 0
    total_rejects = 0
    total_slip = 0

    csv_data = FasterCSV.generate do |csv|
      csv << [
	"Publisher",
	"Ad Network",
	"Tag #",
	"Tag Name",
	time_name,
	"Attempts",
	"Loads",
	"Rejects",
	"Slip",
	"Fill Rate"
      ]
      @fill_stats.each do |fill|
	csv << [
	fill.tag.publisher.site_name,
	fill.tag.network.network_name,
	fill.tag_id,
	fill.tag.tag_name,
	fill.time,
	fill.attempts,
	fill.loads,
	fill.rejects,
	fill.slip,
	fill.fill_rate
	]

	# Add up the totals
        total_attempts += fill.attempts
        total_loads += fill.loads
        total_rejects += fill.rejects
        total_slip += fill.slip
      end
      # Total line
      csv << [
	"Totals",
	"",
	"",
	total_attempts,
	total_loads,
	total_rejects,
	total_slip,
        @fill_stats[0].fill_rate_raw(total_loads, total_attempts)
      ] 
     
    end

    send_data csv_data,
      :type => 'text/csv; header=present',
      :disposition => "attachment; filename=#{@outfile}"

    flash[:notice] = "Export complete, " + @fill_stats.length.to_s + " records exported"

  end
end
