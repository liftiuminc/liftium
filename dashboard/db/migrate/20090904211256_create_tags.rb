class CreateTags < ActiveRecord::Migration
  def self.up
    create_table :tags, { :force => true } do |t|
      t.string :tag_name
      t.references :network
      t.references :publisher
    end


    # Use add column because it supports better options
    add_column :tags, :value, :decimal, {:precision => 3, :scale => 2 }
    add_column :tags, :enabled, :boolean, {:default => 1}
    add_column :tags, :always_fill, :boolean, {:default => 1}
    add_column :tags, :sample_rate, :integer, {:limit => 1}
    add_column :tags, :tier, :integer, {:limit => 3}
    add_column :tags, :frequency_cap, :integer, {:limit => 1}
    add_column :tags, :rejection_cap, :integer, {:limit => 1}
    add_column :tags, :rejection_time, :integer, {:limit => 1}
    add_column :tags, :size, :string
    add_column :tags, :tag, :text
    
  end

  def self.down
    drop_table :tags
  end
end
