class CreateAdformats < ActiveRecord::Migration
  def self.up
    create_table :adformats do |t|
      t.string :format_name
      t.string :size
    end
  end

  def self.down
    drop_table :adformats
  end
end
