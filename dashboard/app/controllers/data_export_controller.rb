class DataExportController < ApplicationController
  before_filter :require_user

  def index
    @limit = 250
    @fill_stats = [] # placeholder empty array in case we don't get that far

    if params[:interval].nil?
      render :action => 'index'
    end

    # FIXME: flash notice isn't clearing until the second time

    case params[:interval]
      when "day" 
        @model = FillsDay
        @time_name = "Day"
      when "hour" 
        @model = FillsHour
        @time_name = "Hour"
      else 
        @model = FillsMinute
        @time_name = "Minute"
    end

    # Sanity checking on dates
    if !params[:date_select].blank? 
	dates = @model.new.get_date_range(params[:date_select])
        params[:start_date] = dates[0]
        params[:end_date] = dates[0]
    end

    if !params[:start_date].blank?
      s = params[:start_date].to_time
      if params[:interval] == "minute" && s + (8*86400) < Time.now 
	flash[:error] = "Please select 'Hour' or 'Day' for interval for dates older than 7 days"
        render :action => 'index'
      elsif params[:interval] != "day" && s + (31*86400) < Time.now 
	flash[:error] = "Please select 'Day' for interval for dates older than 30 days"
        render :action => 'index'
      elsif !params[:end_date].blank? && s > params[:end_date].to_time
	flash[:error] = "Start Date must be before end date"
        render :action => 'index'
      elsif !params[:end_date].blank? && params[:end_date].to_time > Time.now
	flash[:warning] = "Warning: End Date is in the future"
        render :action => 'index'
      end
    end

    if params[:format] != "csv"
      params[:limit] = @limit
    end

    if params[:debug]
      flash[:notice] = "<span style='font-size:smaller'>SQL: " + @model.new.search_sql(@model, params).inspect + "</span>"
    end

    @fill_stats = @model.new.search(@model, params)

    if @fill_stats.length == 0
      flash[:warning] = "No matching stats"
      render :action => 'index'
    elsif @fill_stats.length == @limit
      flash[:warning] = "Limit of #{@limit} reached. Use the CSV option to see all"
    end

    if params[:format] == "csv"
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
	"Size",
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
	fill.tag.size,
	fill.time,
	fill.attempts,
	fill.loads,
	fill.rejects,
	fill.slip,
	fill.fill_rate.to_s + "%"
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
	"",
	"",
	"",
	total_attempts,
	total_loads,
	total_rejects,
	total_slip,
        @fill_stats[0].fill_rate_raw(total_loads, total_attempts).to_s + "%"
      ] 
     
    end

    send_data csv_data,
      :type => 'text/csv; header=present',
      :disposition => "attachment; filename=#{@outfile}"
  end
end
