class FillsBase < ActiveRecord::Base

  belongs_to :tag

  def fill_rate 
    self.fill_rate_raw(loads, attempts)
  end

  def slip
    attempts - (loads + rejects)
  end

  def fill_rate_raw (loads, attempts)
    ((loads.to_f/attempts.to_f).to_f.round(4) * 100)
  end

end
