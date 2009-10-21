class FillsBase < ActiveRecord::Base

  belongs_to :tag

  def fill_rate 
    self.fill_rate_raw(loads, attempts)
  end

  def slip
    attempts - (loads + rejects)
  end

  def fill_rate_raw (loads, attempts)
    if (attempts.to_i.zero?)
	return 0.0
    end
    ((loads.to_f/attempts.to_f).to_f.round(3) * 100)
  end

  def time_name 
    time_column.capitalize
  end 
  
  def search_sql (model, params)

    col = model.new.time_column
    query = []
    query.push("SELECT * FROM " + model.table_name + " INNER JOIN tags ON " + model.table_name + ".tag_id = tags.id WHERE 1=1");

    if (params[:include_disabled].blank?)
       query[0] += " AND enabled = ?"
       query.push(1)
    end

    if (! params[:publisher_id].blank?)
       query[0] += " AND publisher_id = ?"
       query.push(params[:publisher_id].to_i)
    end

    if (! params[:network_id].blank?)
       query[0] += " AND network_id = ?"
       query.push(params[:network_id].to_i)
    end

    if (! params[:size].blank?)
       query[0] += " AND size = ?"
       query.push(params[:size])
    end

    if (! params[:name_search].blank?)
       query[0] += " AND tag_name like ?"
       query.push('%' + params[:name_search] + '%')
    end

    dates = self.get_date_range(params[:date_select])
    params[:start_date] = dates[0].nil? ? "" : dates[0]
    params[:end_date] = dates[1].nil? ? "" : dates[1]

    if (! params[:start_date].blank?)
       query[0] += " AND " + col + " >= ? "
       query.push(params[:start_date].to_time.to_s(:db))
    end

    if (! params[:end_date].blank?)
       query[0] += " AND " + col + " <= ? "
       query.push(params[:end_date].to_time.to_s(:db))
    end

    case (params[:order])
      when "tag_name"
	query[0] += " ORDER BY " + tag_name + " ASC"
      else 
	query[0] += " ORDER BY " + col + " ASC"
    end

    if (! params[:limit].to_s.empty?)
       query[0] += " LIMIT ?"
       query.push(params[:limit].to_i)
 
      if (! params[:offset].blank?)
         query[0] += " OFFSET ? "
         query.push(params[:offset].to_i)
      else
         query[0] += " OFFSET 0"
      end
    end

    return query
      
  end

  def search (model, params)
    model.find_by_sql self.search_sql(model, params)
  end 

  # FIXME: Move somewhere else so that other models can use
  def get_date_range(timeframe)
   now = DateTime.now
   dates = [nil, nil]

   # FIXME: Am I doing this the hard way? Shouldn't this be built into rails?
   # TODO: Timezones
   case timeframe.to_s.downcase
      when "this hour"
	dates[0] = now.strftime('%Y-%m-%d %H:00:00')
      when "last 15 minutes"
	dates[0] = (now - 15.minutes).strftime('%Y-%m-%d %H:%M:00')
	dates[1] = now.strftime('%Y-%m-%d %H:%M:00')
      when "last 60 minutes"
	dates[0] = (now - 60.minutes).strftime('%Y-%m-%d %H:%M:00')
	dates[1] = now.strftime('%Y-%m-%d %H:%M:00')
      when "last hour"
	dates[0] = (now - 1.hour).strftime('%Y-%m-%d %H:00:00')
	dates[1] = now.strftime('%Y-%m-%d %H:00:00')
      when "last 3 hours"
	dates[0] = (now - 3.hours).strftime('%Y-%m-%d %H:00:00')
	dates[1] = now.strftime('%Y-%m-%d %H:00:00')
      when "last 12 hours"
	dates[0] = (now - 12.hours).strftime('%Y-%m-%d %H:00:00')
	dates[1] = now.strftime('%Y-%m-%d %H:00:00')
      when "today"
	dates[0] = now.strftime('%Y-%m-%d 00:00:00')
      when "yesterday"
        dates[0] = (now - 1.day).strftime('%Y-%m-%d 00:00:00')
        dates[1] = now.strftime('%Y-%m-%d 00:00:00')
      when "last 7 days"  || "last week"
        dates[0] = (now - 7.days).strftime('%Y-%m-%d 00:00:00')
        dates[1] = now.strftime('%Y-%m-%d 00:00:00')
      when "this month" || "month to date"
        dates[0] = now.strftime('%Y-%m-01 00:00:00')
        dates[1] = now.strftime('%Y-%m-%d 00:00:00')
      when "last 30 days"
        dates[0] = (now - 30.days).strftime('%Y-%m-%d 00:00:00')
        dates[1] = now.strftime('%Y-%m-%d 00:00:00')
      when "this quarter" || "quarter to date"
        month = now.strftime("%m").to_i
        if month.modulo(3) == 1 # jan, apr, jul, oct
             dates[0] = now.strftime('%Y-%m-01 00:00:00')
        elsif month.modulo(3) == 2 # feb, may, aug, nov
             dates[0] = (now - 1.month).strftime('%Y-%m-01 00:00:00')
        elsif month.modulo(3) == 0 # mar, jun, sep, nov
             dates[0] = (now - 2.month).strftime('%Y-%m-01 00:00:00')
        end
        dates[1] = now.strftime('%Y-%m-%d 00:00:00')

      when "last quarter" 
        month = now.strftime("%m").to_i
        if month.modulo(3) == 1 # jan, apr, jul, oct
             dates[0] = (now - 3.month).strftime('%Y-%m-01 00:00:00')
             dates[1] = now.strftime('%Y-%m-01 00:00:00')
        elsif month.modulo(3) == 2 # feb, may, aug, nov
             dates[0] = (now - 4.month).strftime('%Y-%m-01 00:00:00')
             dates[1] = (now - 1.month).strftime('%Y-%m-01 00:00:00')
        elsif month.modulo(3) == 0 # mar, jun, sep, nov
             dates[0] = (now - 5.month).strftime('%Y-%m-01 00:00:00')
             dates[1] = (now - 2.month).strftime('%Y-%m-01 00:00:00')
        end

      when "this year" || "year to date"
        dates[0] = now.strftime('%Y-01-01 00:00:00')
        dates[1] = now.strftime('%Y-%m-%d 00:00:00')
      when "last year" 
        dates[0] = (now - 1.year).strftime('%Y-01-01 00:00:00')
        dates[1] = now.strftime('%Y-01-01 00:00:00')
      when "all time"
	# no op
    end
    return dates
  end


  def export_to_csv(fill_stats)
    
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
      fill_stats.each do |fill|
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
        fill_stats[0].fill_rate_raw(total_loads, total_attempts).to_s + "%"
      ] 

    end
  end
end
