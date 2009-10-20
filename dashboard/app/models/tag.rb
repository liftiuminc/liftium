class Tag < ActiveRecord::Base

  belongs_to :network
  belongs_to :publisher
  has_many :tag_options, :dependent => :destroy
  has_many :tag_targets, :dependent => :destroy

  ### enable comments on tags. See FB 24
  ### Requires db/migrate/20091013122159_add_tag_comments.rb
  acts_as_commentable

  accepts_nested_attributes_for :tag_options, :allow_destroy => true, :reject_if => proc { |a| a['option_name'].blank? || a['option_value'].blank? }
  accepts_nested_attributes_for :tag_targets, :allow_destroy => true, :reject_if => proc { |a| a['key_name'].blank? || a['key_value'].blank?}

  #TODO: validate publisherid once accounts are set up
  validates_format_of :size, :with => /^[0-9]{1,3}x[0-9]{1,3}$/
  validates_uniqueness_of :tag_name, :scope => :publisher_id
  validates_presence_of :tag_name, :network, :size, :publisher
  validates_inclusion_of :enabled, :in => [true, false]
  validates_inclusion_of :always_fill, :in => [true, false]
  validates_numericality_of :tier, :only_integer => true, :greater_than_or_equal_to => 0, :less_than_or_equal_to => 10, :allow_nil => true
  validates_numericality_of :sample_rate, :greater_than_or_equal_to => 0, :less_than => 100, :allow_nil => true
  validates_numericality_of :frequency_cap, :only_integer => true, :greater_than_or_equal_to => 0, :less_than => 1000, :allow_nil => true
  validates_numericality_of :rejection_time, :only_integer => true, :greater_than_or_equal_to => 0, :less_than => 1440, :allow_nil => true
  validates_numericality_of :value, :greater_than_or_equal_to => 0, :less_than => 100 

  ### From FB 16: Tags page should not allow "Always fill" with a rejection 
  ### time limit set
  validates_each :always_fill do|record, attr, value|
    if value == true and record.rejection_time > 0
      record.errors.add attr, "can not be true if rejection time is set"
    end  
  end

   def enabled_s 
      enabled ? "Yes" : "No"
   end

   def always_fill_s 
      always_fill ? "Yes" : "No"
   end

   # db returns 0.1. we want this to be 0.10
   def value_s 
      sprintf( "%.2f", value)
   end

   def html 
      if tag 
	"#{tag}"
      else 
        # TODO: Network tag options expansion
	"#{tag.network.tag_template}"
      end
   end

   def width
     @d = size.to_s.split("x")
     @d[0] || 0
   end

   def height
     @d = size.to_s.split("x")
     @d[1] || 0
   end

   def css_size 
     "width:#{width}px;height:#{height}px;"
   end

   def preview_url 
     env = Rails.configuration.environment
     if env == "development" || env == "dev_mysql"
	"http://delivery.dev.liftium.com/tag?tag_id=#{id}"
     else 
	"http://delivery.liftium.com/tag?tag_id=#{id}"
     end
   end 

  def search_sql (params)

    # FIXME: There has to be a better way...
    adapter = Rails.configuration.database_configuration[Rails.configuration.environment]["adapter"]
    if adapter == "sqlite3"
      # sqllite calls it rand
      random = "random"
    else 
      # mysql calls it rand
      random = "rand"
    end

    query = []
    query.push("SELECT *, (" +  random + "() * (0.1 * value)) AS weighted_random_value
		FROM tags WHERE 1=1");

    if (params[:include_disabled].blank?)
       query[0] += " AND enabled = ?"
       query.push(true)
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

    ### search for both name & ids
    if (! params[:name_search].blank?)
       query[0] += " AND (tag_name like ? OR id = ?) "
       query.push('%' + params[:name_search] + '%')
       query.push( params[:name_search] )       
    end

    case (params[:order])
      when "tag_name"
	query[0] += " ORDER BY tag_name ASC"
      else 
        # Same order as the chain (without the randomization)
	query[0] += " ORDER BY tier ASC, value DESC"
    end

    if (! params[:limit].to_s.empty? && params[:limit].to_i < 100)
       query[0] += " LIMIT ?"
       query.push(params[:limit].to_i)
    else
       query[0] += " LIMIT 50"
    end

    if (! params[:offset].blank?)
       query[0] += " OFFSET ? "
       query.push(params[:offset].to_i)
    else
       query[0] += " OFFSET 0"
    end

    return query
      
  end

  def search (params)
    Tag.find_by_sql self.search_sql(params)
  end 


  def get_fill_stats (range)
    conditions = ["tag_id = ?", id]
    dates = FillsMinute.new.get_date_range(range)
    if dates[0]
      conditions[0] += " AND minute >= ?"
      conditions.push(dates[0])
    end
    if dates[1]
      conditions[0] += " AND minute <= ?"
      conditions.push(dates[1])
    end

    loads = FillsMinute.sum("loads", :conditions => conditions)
    attempts = FillsMinute.sum("attempts", :conditions => conditions)
    rejects = FillsMinute.sum("rejects", :conditions => conditions)

    fill_rate = FillsMinute.new.fill_rate_raw(loads, attempts)
    return {:loads => loads, :attempts => attempts, :rejects => rejects, :fill_rate => fill_rate}
  end 

end
