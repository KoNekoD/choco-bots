<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240112180808
    extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE choco_chat (id VARCHAR(255) NOT NULL, configuration_id VARCHAR(255) DEFAULT NULL, update_chat_id VARCHAR(26) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_8A4CD8F573F32DD8 ON choco_chat (configuration_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_8A4CD8F55D2B68DC ON choco_chat (update_chat_id)'
        );
        $this->addSql(
            'CREATE TABLE choco_chat_configuration (id VARCHAR(255) NOT NULL, notify_about_new_chat_member_in_system BOOLEAN NOT NULL, mute_enabled BOOLEAN NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE choco_chat_member (id VARCHAR(26) NOT NULL, rank_id VARCHAR(255) DEFAULT NULL, chat_id VARCHAR(255) DEFAULT NULL, user_id VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, since_spent_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, reputation INT NOT NULL, reputation_change_quota_last_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, reputation_change_quota INT NOT NULL, status SMALLINT NOT NULL, custom_title VARCHAR(255) DEFAULT NULL, is_anonymous BOOLEAN DEFAULT NULL, until_date INT DEFAULT NULL, can_be_edited BOOLEAN DEFAULT NULL, can_post_messages BOOLEAN DEFAULT NULL, can_edit_messages BOOLEAN DEFAULT NULL, can_delete_messages BOOLEAN DEFAULT NULL, can_promote_members BOOLEAN DEFAULT NULL, can_change_info BOOLEAN DEFAULT NULL, can_invite_users BOOLEAN DEFAULT NULL, can_pin_messages BOOLEAN DEFAULT NULL, can_send_messages BOOLEAN DEFAULT NULL, can_send_media_messages BOOLEAN DEFAULT NULL, can_send_polls BOOLEAN DEFAULT NULL, can_send_other_messages BOOLEAN DEFAULT NULL, can_add_web_page_previews BOOLEAN DEFAULT NULL, can_manage_chat BOOLEAN DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_5E1B75D07616678F ON choco_chat_member (rank_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_5E1B75D01A9A7125 ON choco_chat_member (chat_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_5E1B75D0A76ED395 ON choco_chat_member (user_id)'
        );
        $this->addSql(
            'COMMENT ON COLUMN choco_chat_member.created_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'COMMENT ON COLUMN choco_chat_member.since_spent_time IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'COMMENT ON COLUMN choco_chat_member.reputation_change_quota_last_updated IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'CREATE TABLE choco_chat_member_rank (id VARCHAR(255) NOT NULL, rank SMALLINT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE choco_chat_member_warn (id VARCHAR(255) NOT NULL, warned_chat_member_id VARCHAR(26) DEFAULT NULL, creator_chat_member_id VARCHAR(26) DEFAULT NULL, chat_id VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expired BOOLEAN NOT NULL, reason VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_95C0D2A28A3A6A3C ON choco_chat_member_warn (warned_chat_member_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_95C0D2A2D0D8C278 ON choco_chat_member_warn (creator_chat_member_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_95C0D2A21A9A7125 ON choco_chat_member_warn (chat_id)'
        );
        $this->addSql(
            'COMMENT ON COLUMN choco_chat_member_warn.created_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'COMMENT ON COLUMN choco_chat_member_warn.expires_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'CREATE TABLE choco_marry (id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, marry_general_status VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'COMMENT ON COLUMN choco_marry.created_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'CREATE TABLE choco_user (id VARCHAR(255) NOT NULL, marry_id VARCHAR(255) DEFAULT NULL, update_user_id VARCHAR(26) DEFAULT NULL, marry_participant_status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_6242FC1649C2C103 ON choco_user (marry_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_6242FC16E0DFCA6C ON choco_user (update_user_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_animation (id VARCHAR(26) NOT NULL, thumb_id VARCHAR(26) DEFAULT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, duration INT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_D13B5ACBC7034EA5 ON updates_animation (thumb_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_audio (id VARCHAR(26) NOT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, duration INT NOT NULL, performer VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_callback_query (id VARCHAR(26) NOT NULL, telegram_id VARCHAR(255) NOT NULL, inline_message_id VARCHAR(255) DEFAULT NULL, chat_instance VARCHAR(255) DEFAULT NULL, data VARCHAR(255) DEFAULT NULL, game_short_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_chat_location (id VARCHAR(26) NOT NULL, location_id VARCHAR(26) DEFAULT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_F687E4D664D218E ON updates_chat_location (location_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_chat_permissions (id VARCHAR(26) NOT NULL, can_send_messages BOOLEAN DEFAULT NULL, can_send_media_messages BOOLEAN DEFAULT NULL, can_send_polls BOOLEAN DEFAULT NULL, can_send_other_messages BOOLEAN DEFAULT NULL, can_add_web_page_previews BOOLEAN DEFAULT NULL, can_change_info BOOLEAN DEFAULT NULL, can_invite_users BOOLEAN DEFAULT NULL, can_pin_messages BOOLEAN DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_chat_photo (file_id VARCHAR(255) NOT NULL, PRIMARY KEY(file_id))'
        );
        $this->addSql(
            'CREATE TABLE updates_contact (id VARCHAR(26) NOT NULL, phone_number VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, vcard VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_document (id VARCHAR(26) NOT NULL, thumb_id VARCHAR(26) DEFAULT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, file_name VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_2DD99CDC7034EA5 ON updates_document (thumb_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_invoice (id VARCHAR(26) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, start_parameter VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, total_amount INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_location (id VARCHAR(26) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, horizontal_accuracy DOUBLE PRECISION DEFAULT NULL, live_period INT DEFAULT NULL, heading INT DEFAULT NULL, proximity_alert_radius INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_mask_position (id VARCHAR(26) NOT NULL, point VARCHAR(255) NOT NULL, x_shift DOUBLE PRECISION NOT NULL, y_shift DOUBLE PRECISION NOT NULL, scale DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_message_entity (id VARCHAR(26) NOT NULL, user_id VARCHAR(26) DEFAULT NULL, type VARCHAR(255) NOT NULL, message_offset INT NOT NULL, length INT NOT NULL, url VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_7C4CBEA8A76ED395 ON updates_message_entity (user_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_order_info (id VARCHAR(26) NOT NULL, shipping_address_id VARCHAR(26) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_5F7E7594D4CFF2B ON updates_order_info (shipping_address_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_photo_size (id VARCHAR(26) NOT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_poll (id VARCHAR(26) NOT NULL, source_poll_id VARCHAR(255) NOT NULL, question VARCHAR(255) NOT NULL, total_voter_count INT NOT NULL, is_closed BOOLEAN NOT NULL, is_anonymous BOOLEAN NOT NULL, type VARCHAR(255) NOT NULL, allow_multiple_answers BOOLEAN DEFAULT NULL, correct_option_id INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_poll_polls_poll_options (poll_id VARCHAR(26) NOT NULL, option_id VARCHAR(26) NOT NULL, PRIMARY KEY(poll_id, option_id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_C2B3E1CC3C947C0F ON updates_poll_polls_poll_options (poll_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_C2B3E1CCA7C41D6F ON updates_poll_polls_poll_options (option_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_poll_option (id VARCHAR(26) NOT NULL, text VARCHAR(255) NOT NULL, voter_count INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_shipping_address (id VARCHAR(26) NOT NULL, country_code VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, street_line1 VARCHAR(255) NOT NULL, street_line2 VARCHAR(255) NOT NULL, post_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_sticker (id VARCHAR(26) NOT NULL, thumb_id VARCHAR(26) DEFAULT NULL, mask_position_id VARCHAR(26) DEFAULT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, is_animated BOOLEAN NOT NULL, emoji VARCHAR(255) DEFAULT NULL, set_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_5B46D796C7034EA5 ON updates_sticker (thumb_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_5B46D796A0ADB334 ON updates_sticker (mask_position_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_successful_payment (id VARCHAR(26) NOT NULL, order_info_id VARCHAR(26) DEFAULT NULL, currency VARCHAR(255) NOT NULL, total_amount INT NOT NULL, invoice_payload JSON NOT NULL, shipping_option_id VARCHAR(255) DEFAULT NULL, telegram_payment_charge_id VARCHAR(255) NOT NULL, provider_payment_charge_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_1BF1405FABF168B3 ON updates_successful_payment (order_info_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update (id VARCHAR(26) NOT NULL, chat_id VARCHAR(26) DEFAULT NULL, message_id VARCHAR(26) DEFAULT NULL, callback_query_id VARCHAR(26) DEFAULT NULL, handle_status SMALLINT NOT NULL, handle_retries_count SMALLINT NOT NULL, source_update_id BIGINT NOT NULL, source_service_name VARCHAR(255) NOT NULL, bot_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_887EBC1BBD08797F ON updates_update (source_update_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_887EBC1B1A9A7125 ON updates_update (chat_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_887EBC1B537A1329 ON updates_update (message_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_887EBC1BC56E6DB9 ON updates_update (callback_query_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update_chat (id VARCHAR(26) NOT NULL, photo_id VARCHAR(255) DEFAULT NULL, pinned_message_id VARCHAR(26) DEFAULT NULL, permissions_id VARCHAR(26) DEFAULT NULL, location_id VARCHAR(26) DEFAULT NULL, source_chat_id BIGINT NOT NULL, source_service_name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, bio VARCHAR(255) DEFAULT NULL, has_private_forwards BOOLEAN DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, invite_link VARCHAR(255) DEFAULT NULL, slow_mode_delay INT DEFAULT NULL, has_protected_content BOOLEAN DEFAULT NULL, sticker_set_name VARCHAR(255) DEFAULT NULL, can_set_sticker_set BOOLEAN DEFAULT NULL, linked_chat_id INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_D51982195345C94D ON updates_update_chat (source_chat_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_D51982197E9E4C8C ON updates_update_chat (photo_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_D5198219C254572F ON updates_update_chat (pinned_message_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_D51982199C3E4F87 ON updates_update_chat (permissions_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_D519821964D218E ON updates_update_chat (location_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update_message (id VARCHAR(26) NOT NULL, from_id VARCHAR(26) DEFAULT NULL, chat_id VARCHAR(26) DEFAULT NULL, forward_from_id VARCHAR(26) DEFAULT NULL, forward_from_chat_id VARCHAR(26) DEFAULT NULL, reply_to_message_id VARCHAR(26) DEFAULT NULL, audio_id VARCHAR(26) DEFAULT NULL, document_id VARCHAR(26) DEFAULT NULL, animation_id VARCHAR(26) DEFAULT NULL, sticker_id VARCHAR(26) DEFAULT NULL, video_id VARCHAR(26) DEFAULT NULL, voice_id VARCHAR(26) DEFAULT NULL, contact_id VARCHAR(26) DEFAULT NULL, location_id VARCHAR(26) DEFAULT NULL, venue_id VARCHAR(26) DEFAULT NULL, poll_id VARCHAR(26) DEFAULT NULL, left_chat_member_id VARCHAR(26) DEFAULT NULL, pinned_message_id VARCHAR(26) DEFAULT NULL, invoice_id VARCHAR(26) DEFAULT NULL, successful_payment_id VARCHAR(26) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, source_message_id INT NOT NULL, source_service_name VARCHAR(255) NOT NULL, date INT NOT NULL, forward_from_message_id INT DEFAULT NULL, forward_signature VARCHAR(255) DEFAULT NULL, forward_sender_name VARCHAR(255) DEFAULT NULL, forward_date INT DEFAULT NULL, edit_date INT DEFAULT NULL, media_group_id BIGINT DEFAULT NULL, author_signature VARCHAR(255) DEFAULT NULL, text TEXT DEFAULT NULL, caption TEXT DEFAULT NULL, new_chat_title VARCHAR(255) DEFAULT NULL, delete_chat_photo BOOLEAN DEFAULT NULL, group_chat_created BOOLEAN DEFAULT NULL, supergroup_chat_created BOOLEAN DEFAULT NULL, channel_chat_created BOOLEAN DEFAULT NULL, migrate_to_chat_id BIGINT DEFAULT NULL, migrate_from_chat_id BIGINT DEFAULT NULL, connected_website VARCHAR(255) DEFAULT NULL, reply_markup TEXT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_B44F4E4978CED90B ON updates_update_message (from_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_B44F4E491A9A7125 ON updates_update_message (chat_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_B44F4E49ECE92377 ON updates_update_message (forward_from_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_B44F4E49611276FC ON updates_update_message (forward_from_chat_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_B44F4E495518525D ON updates_update_message (reply_to_message_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E493A3123C7 ON updates_update_message (audio_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E49C33F7837 ON updates_update_message (document_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E493858647E ON updates_update_message (animation_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E494D965A4D ON updates_update_message (sticker_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E4929C1004E ON updates_update_message (video_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E491672336E ON updates_update_message (voice_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E49E7A1254A ON updates_update_message (contact_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E4964D218E ON updates_update_message (location_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E4940A73EBA ON updates_update_message (venue_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E493C947C0F ON updates_update_message (poll_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E49D52A6AF6 ON updates_update_message (left_chat_member_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E49C254572F ON updates_update_message (pinned_message_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E492989F1FD ON updates_update_message (invoice_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B44F4E49A75F7B3F ON updates_update_message (successful_payment_id)'
        );
        $this->addSql(
            'COMMENT ON COLUMN updates_update_message.created_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'CREATE TABLE updates_update_message_messages_entities (message_id VARCHAR(26) NOT NULL, entity_id VARCHAR(26) NOT NULL, PRIMARY KEY(message_id, entity_id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_7B75222B537A1329 ON updates_update_message_messages_entities (message_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_7B75222B81257D5D ON updates_update_message_messages_entities (entity_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update_message_messages_caption_entities (message_id VARCHAR(26) NOT NULL, entity_id VARCHAR(26) NOT NULL, PRIMARY KEY(message_id, entity_id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_7286302B537A1329 ON updates_update_message_messages_caption_entities (message_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_7286302B81257D5D ON updates_update_message_messages_caption_entities (entity_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update_message_messages_photos (message_id VARCHAR(26) NOT NULL, photo_id VARCHAR(26) NOT NULL, PRIMARY KEY(message_id, photo_id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_16C5D726537A1329 ON updates_update_message_messages_photos (message_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_16C5D7267E9E4C8C ON updates_update_message_messages_photos (photo_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update_message_messages_new_chat_members (message_id VARCHAR(26) NOT NULL, user_id VARCHAR(26) NOT NULL, PRIMARY KEY(message_id, user_id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_936DA4F1537A1329 ON updates_update_message_messages_new_chat_members (message_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_936DA4F1A76ED395 ON updates_update_message_messages_new_chat_members (user_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update_message_messages_new_chat_photos (message_id VARCHAR(26) NOT NULL, photo_id VARCHAR(26) NOT NULL, PRIMARY KEY(message_id, photo_id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_13A30679537A1329 ON updates_update_message_messages_new_chat_photos (message_id)'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_13A306797E9E4C8C ON updates_update_message_messages_new_chat_photos (photo_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_update_user (id VARCHAR(26) NOT NULL, source_user_id BIGINT NOT NULL, source_service_name VARCHAR(255) NOT NULL, is_bot BOOLEAN NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, language_code VARCHAR(255) DEFAULT NULL, can_join_groups BOOLEAN DEFAULT NULL, can_read_all_group_messages BOOLEAN DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE updates_venue (id VARCHAR(26) NOT NULL, location_id VARCHAR(26) DEFAULT NULL, title VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, foursquare_id VARCHAR(255) DEFAULT NULL, foursquare_type VARCHAR(255) DEFAULT NULL, google_place_id VARCHAR(255) DEFAULT NULL, google_place_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_1062229764D218E ON updates_venue (location_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_video (id VARCHAR(26) NOT NULL, thumb_id VARCHAR(26) DEFAULT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, duration INT NOT NULL, mime_type VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_FD34E3B6C7034EA5 ON updates_video (thumb_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_video_note (id VARCHAR(26) NOT NULL, thumb_id VARCHAR(26) DEFAULT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, length INT NOT NULL, duration INT NOT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_4BA54BD1C7034EA5 ON updates_video_note (thumb_id)'
        );
        $this->addSql(
            'CREATE TABLE updates_voice (id VARCHAR(26) NOT NULL, file_id VARCHAR(255) NOT NULL, file_unique_id VARCHAR(255) NOT NULL, duration INT NOT NULL, mime_type VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)'
        );
        $this->addSql(
            'CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)'
        );
        $this->addSql(
            'CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)'
        );
        $this->addSql(
            'COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\''
        );
        $this->addSql(
            'CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;'
        );
        $this->addSql(
            'DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;'
        );
        $this->addSql(
            'CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();'
        );
        $this->addSql(
            'ALTER TABLE choco_chat ADD CONSTRAINT FK_8A4CD8F573F32DD8 FOREIGN KEY (configuration_id) REFERENCES choco_chat_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_chat ADD CONSTRAINT FK_8A4CD8F55D2B68DC FOREIGN KEY (update_chat_id) REFERENCES updates_update_chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member ADD CONSTRAINT FK_5E1B75D07616678F FOREIGN KEY (rank_id) REFERENCES choco_chat_member_rank (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member ADD CONSTRAINT FK_5E1B75D01A9A7125 FOREIGN KEY (chat_id) REFERENCES choco_chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member ADD CONSTRAINT FK_5E1B75D0A76ED395 FOREIGN KEY (user_id) REFERENCES choco_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member_warn ADD CONSTRAINT FK_95C0D2A28A3A6A3C FOREIGN KEY (warned_chat_member_id) REFERENCES choco_chat_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member_warn ADD CONSTRAINT FK_95C0D2A2D0D8C278 FOREIGN KEY (creator_chat_member_id) REFERENCES choco_chat_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member_warn ADD CONSTRAINT FK_95C0D2A21A9A7125 FOREIGN KEY (chat_id) REFERENCES choco_chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_user ADD CONSTRAINT FK_6242FC1649C2C103 FOREIGN KEY (marry_id) REFERENCES choco_marry (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE choco_user ADD CONSTRAINT FK_6242FC16E0DFCA6C FOREIGN KEY (update_user_id) REFERENCES updates_update_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_animation ADD CONSTRAINT FK_D13B5ACBC7034EA5 FOREIGN KEY (thumb_id) REFERENCES updates_photo_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_chat_location ADD CONSTRAINT FK_F687E4D664D218E FOREIGN KEY (location_id) REFERENCES updates_location (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_document ADD CONSTRAINT FK_2DD99CDC7034EA5 FOREIGN KEY (thumb_id) REFERENCES updates_photo_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_message_entity ADD CONSTRAINT FK_7C4CBEA8A76ED395 FOREIGN KEY (user_id) REFERENCES updates_update_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_order_info ADD CONSTRAINT FK_5F7E7594D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES updates_shipping_address (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_poll_polls_poll_options ADD CONSTRAINT FK_C2B3E1CC3C947C0F FOREIGN KEY (poll_id) REFERENCES updates_poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_poll_polls_poll_options ADD CONSTRAINT FK_C2B3E1CCA7C41D6F FOREIGN KEY (option_id) REFERENCES updates_poll_option (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_sticker ADD CONSTRAINT FK_5B46D796C7034EA5 FOREIGN KEY (thumb_id) REFERENCES updates_photo_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_sticker ADD CONSTRAINT FK_5B46D796A0ADB334 FOREIGN KEY (mask_position_id) REFERENCES updates_mask_position (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_successful_payment ADD CONSTRAINT FK_1BF1405FABF168B3 FOREIGN KEY (order_info_id) REFERENCES updates_order_info (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update ADD CONSTRAINT FK_887EBC1B1A9A7125 FOREIGN KEY (chat_id) REFERENCES updates_update_chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update ADD CONSTRAINT FK_887EBC1B537A1329 FOREIGN KEY (message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update ADD CONSTRAINT FK_887EBC1BC56E6DB9 FOREIGN KEY (callback_query_id) REFERENCES updates_callback_query (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat ADD CONSTRAINT FK_D51982197E9E4C8C FOREIGN KEY (photo_id) REFERENCES updates_chat_photo (file_id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat ADD CONSTRAINT FK_D5198219C254572F FOREIGN KEY (pinned_message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat ADD CONSTRAINT FK_D51982199C3E4F87 FOREIGN KEY (permissions_id) REFERENCES updates_chat_permissions (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat ADD CONSTRAINT FK_D519821964D218E FOREIGN KEY (location_id) REFERENCES updates_chat_location (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E4978CED90B FOREIGN KEY (from_id) REFERENCES updates_update_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E491A9A7125 FOREIGN KEY (chat_id) REFERENCES updates_update_chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E49ECE92377 FOREIGN KEY (forward_from_id) REFERENCES updates_update_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E49611276FC FOREIGN KEY (forward_from_chat_id) REFERENCES updates_update_chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E495518525D FOREIGN KEY (reply_to_message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E493A3123C7 FOREIGN KEY (audio_id) REFERENCES updates_audio (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E49C33F7837 FOREIGN KEY (document_id) REFERENCES updates_document (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E493858647E FOREIGN KEY (animation_id) REFERENCES updates_animation (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E494D965A4D FOREIGN KEY (sticker_id) REFERENCES updates_sticker (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E4929C1004E FOREIGN KEY (video_id) REFERENCES updates_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E491672336E FOREIGN KEY (voice_id) REFERENCES updates_voice (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E49E7A1254A FOREIGN KEY (contact_id) REFERENCES updates_contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E4964D218E FOREIGN KEY (location_id) REFERENCES updates_location (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E4940A73EBA FOREIGN KEY (venue_id) REFERENCES updates_venue (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E493C947C0F FOREIGN KEY (poll_id) REFERENCES updates_poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E49D52A6AF6 FOREIGN KEY (left_chat_member_id) REFERENCES updates_update_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E49C254572F FOREIGN KEY (pinned_message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E492989F1FD FOREIGN KEY (invoice_id) REFERENCES updates_invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message ADD CONSTRAINT FK_B44F4E49A75F7B3F FOREIGN KEY (successful_payment_id) REFERENCES updates_successful_payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_entities ADD CONSTRAINT FK_7B75222B537A1329 FOREIGN KEY (message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_entities ADD CONSTRAINT FK_7B75222B81257D5D FOREIGN KEY (entity_id) REFERENCES updates_message_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_caption_entities ADD CONSTRAINT FK_7286302B537A1329 FOREIGN KEY (message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_caption_entities ADD CONSTRAINT FK_7286302B81257D5D FOREIGN KEY (entity_id) REFERENCES updates_message_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_photos ADD CONSTRAINT FK_16C5D726537A1329 FOREIGN KEY (message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_photos ADD CONSTRAINT FK_16C5D7267E9E4C8C FOREIGN KEY (photo_id) REFERENCES updates_photo_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_members ADD CONSTRAINT FK_936DA4F1537A1329 FOREIGN KEY (message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_members ADD CONSTRAINT FK_936DA4F1A76ED395 FOREIGN KEY (user_id) REFERENCES updates_update_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_photos ADD CONSTRAINT FK_13A30679537A1329 FOREIGN KEY (message_id) REFERENCES updates_update_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_photos ADD CONSTRAINT FK_13A306797E9E4C8C FOREIGN KEY (photo_id) REFERENCES updates_photo_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_venue ADD CONSTRAINT FK_1062229764D218E FOREIGN KEY (location_id) REFERENCES updates_location (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_video ADD CONSTRAINT FK_FD34E3B6C7034EA5 FOREIGN KEY (thumb_id) REFERENCES updates_photo_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE updates_video_note ADD CONSTRAINT FK_4BA54BD1C7034EA5 FOREIGN KEY (thumb_id) REFERENCES updates_photo_size (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql(
            'ALTER TABLE choco_chat DROP CONSTRAINT FK_8A4CD8F573F32DD8'
        );
        $this->addSql(
            'ALTER TABLE choco_chat DROP CONSTRAINT FK_8A4CD8F55D2B68DC'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member DROP CONSTRAINT FK_5E1B75D07616678F'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member DROP CONSTRAINT FK_5E1B75D01A9A7125'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member DROP CONSTRAINT FK_5E1B75D0A76ED395'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member_warn DROP CONSTRAINT FK_95C0D2A28A3A6A3C'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member_warn DROP CONSTRAINT FK_95C0D2A2D0D8C278'
        );
        $this->addSql(
            'ALTER TABLE choco_chat_member_warn DROP CONSTRAINT FK_95C0D2A21A9A7125'
        );
        $this->addSql(
            'ALTER TABLE choco_user DROP CONSTRAINT FK_6242FC1649C2C103'
        );
        $this->addSql(
            'ALTER TABLE choco_user DROP CONSTRAINT FK_6242FC16E0DFCA6C'
        );
        $this->addSql(
            'ALTER TABLE updates_animation DROP CONSTRAINT FK_D13B5ACBC7034EA5'
        );
        $this->addSql(
            'ALTER TABLE updates_chat_location DROP CONSTRAINT FK_F687E4D664D218E'
        );
        $this->addSql(
            'ALTER TABLE updates_document DROP CONSTRAINT FK_2DD99CDC7034EA5'
        );
        $this->addSql(
            'ALTER TABLE updates_message_entity DROP CONSTRAINT FK_7C4CBEA8A76ED395'
        );
        $this->addSql(
            'ALTER TABLE updates_order_info DROP CONSTRAINT FK_5F7E7594D4CFF2B'
        );
        $this->addSql(
            'ALTER TABLE updates_poll_polls_poll_options DROP CONSTRAINT FK_C2B3E1CC3C947C0F'
        );
        $this->addSql(
            'ALTER TABLE updates_poll_polls_poll_options DROP CONSTRAINT FK_C2B3E1CCA7C41D6F'
        );
        $this->addSql(
            'ALTER TABLE updates_sticker DROP CONSTRAINT FK_5B46D796C7034EA5'
        );
        $this->addSql(
            'ALTER TABLE updates_sticker DROP CONSTRAINT FK_5B46D796A0ADB334'
        );
        $this->addSql(
            'ALTER TABLE updates_successful_payment DROP CONSTRAINT FK_1BF1405FABF168B3'
        );
        $this->addSql(
            'ALTER TABLE updates_update DROP CONSTRAINT FK_887EBC1B1A9A7125'
        );
        $this->addSql(
            'ALTER TABLE updates_update DROP CONSTRAINT FK_887EBC1B537A1329'
        );
        $this->addSql(
            'ALTER TABLE updates_update DROP CONSTRAINT FK_887EBC1BC56E6DB9'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat DROP CONSTRAINT FK_D51982197E9E4C8C'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat DROP CONSTRAINT FK_D5198219C254572F'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat DROP CONSTRAINT FK_D51982199C3E4F87'
        );
        $this->addSql(
            'ALTER TABLE updates_update_chat DROP CONSTRAINT FK_D519821964D218E'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E4978CED90B'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E491A9A7125'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E49ECE92377'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E49611276FC'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E495518525D'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E493A3123C7'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E49C33F7837'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E493858647E'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E494D965A4D'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E4929C1004E'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E491672336E'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E49E7A1254A'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E4964D218E'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E4940A73EBA'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E493C947C0F'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E49D52A6AF6'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E49C254572F'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E492989F1FD'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message DROP CONSTRAINT FK_B44F4E49A75F7B3F'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_entities DROP CONSTRAINT FK_7B75222B537A1329'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_entities DROP CONSTRAINT FK_7B75222B81257D5D'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_caption_entities DROP CONSTRAINT FK_7286302B537A1329'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_caption_entities DROP CONSTRAINT FK_7286302B81257D5D'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_photos DROP CONSTRAINT FK_16C5D726537A1329'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_photos DROP CONSTRAINT FK_16C5D7267E9E4C8C'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_members DROP CONSTRAINT FK_936DA4F1537A1329'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_members DROP CONSTRAINT FK_936DA4F1A76ED395'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_photos DROP CONSTRAINT FK_13A30679537A1329'
        );
        $this->addSql(
            'ALTER TABLE updates_update_message_messages_new_chat_photos DROP CONSTRAINT FK_13A306797E9E4C8C'
        );
        $this->addSql(
            'ALTER TABLE updates_venue DROP CONSTRAINT FK_1062229764D218E'
        );
        $this->addSql(
            'ALTER TABLE updates_video DROP CONSTRAINT FK_FD34E3B6C7034EA5'
        );
        $this->addSql(
            'ALTER TABLE updates_video_note DROP CONSTRAINT FK_4BA54BD1C7034EA5'
        );
        $this->addSql('DROP TABLE choco_chat');
        $this->addSql('DROP TABLE choco_chat_configuration');
        $this->addSql('DROP TABLE choco_chat_member');
        $this->addSql('DROP TABLE choco_chat_member_rank');
        $this->addSql('DROP TABLE choco_chat_member_warn');
        $this->addSql('DROP TABLE choco_marry');
        $this->addSql('DROP TABLE choco_user');
        $this->addSql('DROP TABLE updates_animation');
        $this->addSql('DROP TABLE updates_audio');
        $this->addSql('DROP TABLE updates_callback_query');
        $this->addSql('DROP TABLE updates_chat_location');
        $this->addSql('DROP TABLE updates_chat_permissions');
        $this->addSql('DROP TABLE updates_chat_photo');
        $this->addSql('DROP TABLE updates_contact');
        $this->addSql('DROP TABLE updates_document');
        $this->addSql('DROP TABLE updates_invoice');
        $this->addSql('DROP TABLE updates_location');
        $this->addSql('DROP TABLE updates_mask_position');
        $this->addSql('DROP TABLE updates_message_entity');
        $this->addSql('DROP TABLE updates_order_info');
        $this->addSql('DROP TABLE updates_photo_size');
        $this->addSql('DROP TABLE updates_poll');
        $this->addSql('DROP TABLE updates_poll_polls_poll_options');
        $this->addSql('DROP TABLE updates_poll_option');
        $this->addSql('DROP TABLE updates_shipping_address');
        $this->addSql('DROP TABLE updates_sticker');
        $this->addSql('DROP TABLE updates_successful_payment');
        $this->addSql('DROP TABLE updates_update');
        $this->addSql('DROP TABLE updates_update_chat');
        $this->addSql('DROP TABLE updates_update_message');
        $this->addSql('DROP TABLE updates_update_message_messages_entities');
        $this->addSql(
            'DROP TABLE updates_update_message_messages_caption_entities'
        );
        $this->addSql('DROP TABLE updates_update_message_messages_photos');
        $this->addSql(
            'DROP TABLE updates_update_message_messages_new_chat_members'
        );
        $this->addSql(
            'DROP TABLE updates_update_message_messages_new_chat_photos'
        );
        $this->addSql('DROP TABLE updates_update_user');
        $this->addSql('DROP TABLE updates_venue');
        $this->addSql('DROP TABLE updates_video');
        $this->addSql('DROP TABLE updates_video_note');
        $this->addSql('DROP TABLE updates_voice');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
