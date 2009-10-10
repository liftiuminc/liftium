class ConvertSampleRateToDecimal < ActiveRecord::Migration
  def self.up
    change_column :tags, :sample_rate, :decimal, {:precision=> 5, :scale => 2}
  end

  def self.down
    change_column :tags, :sample_rate, :integer
  end
end
