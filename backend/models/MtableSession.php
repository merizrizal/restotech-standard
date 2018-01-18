<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "mtable_session".
 *
 * @property string $id
 * @property string $mtable_id
 * @property string $nama_tamu
 * @property integer $jumlah_tamu
 * @property string $catatan
 * @property string $jumlah_harga
 * @property string $discount_type
 * @property string $discount
 * @property double $pajak
 * @property double $service_charge
 * @property string $opened_at
 * @property string $user_opened
 * @property integer $is_closed
 * @property string $closed_at
 * @property string $user_closed
 * @property integer $is_join_mtable
 * @property integer $bill_printed
 * @property integer $is_paid
 * @property string $created_at
 * @property string $user_created
 * @property string $updated_at
 * @property string $user_updated
 *
 * @property MtableJoin[] $mtableJoins
 * @property MtableOrder[] $mtableOrders
 * @property Mtable $mtable
 * @property User $userOpened
 * @property User $userClosed
 * @property User $userCreated
 * @property User $userUpdated
 * @property MtableSessionJoin $mtableSessionJoin
 * @property SaleInvoice[] $saleInvoices
 * @property SaleInvoiceCorrection[] $saleInvoiceCorrections
 */
class MtableSession extends \sybase\SybaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtable_session';
    }
        

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mtable_id'], 'required'],
            [['jumlah_tamu', 'is_closed', 'is_join_mtable', 'bill_printed', 'is_paid'], 'integer'],
            [['jumlah_harga', 'discount', 'pajak', 'service_charge'], 'number'],
            [['catatan', 'discount_type'], 'string'],
            [['opened_at', 'closed_at', 'created_at', 'updated_at'], 'safe'],
            [['mtable_id'], 'string', 'max' => 24],
            [['nama_tamu'], 'string', 'max' => 64],
            [['user_opened', 'user_closed', 'user_created', 'user_updated'], 'string', 'max' => 32],
            [['mtable_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mtable::className(), 'targetAttribute' => ['mtable_id' => 'id']],
            [['user_opened'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_opened' => 'id']],
            [['user_closed'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_closed' => 'id']],
            [['user_created'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created' => 'id']],
            [['user_updated'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_updated' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mtable_id' => 'Mtable ID',
            'nama_tamu' => 'Nama Tamu',
            'catatan' => 'Catatan',
            'jumlah_tamu' => 'Jumlah Tamu',
            'jumlah_harga' => 'Jumlah Harga',
            'discount_type' => 'Discount Type',
            'discount' => 'Discount',
            'pajak' => 'Pajak',
            'service_charge' => 'Service Charge',
            'opened_at' => 'Opened At',
            'user_opened' => 'User Opened',
            'is_closed' => 'Is Closed',
            'closed_at' => 'Closed At',
            'user_closed' => 'User Closed',
            'is_join_mtable' => 'Is Join Mtable',
            'bill_printed' => 'Bill Printed',
            'is_paid' => 'Is Paid',
            'created_at' => 'Created At',
            'user_created' => 'User Created',
            'updated_at' => 'Updated At',
            'user_updated' => 'User Updated',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableJoins()
    {
        return $this->hasMany(MtableJoin::className(), ['active_mtable_session_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableOrders()
    {
        return $this->hasMany(MtableOrder::className(), ['mtable_session_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtable()
    {
        return $this->hasOne(Mtable::className(), ['id' => 'mtable_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserOpened()
    {
        return $this->hasOne(User::className(), ['id' => 'user_opened']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserClosed()
    {
        return $this->hasOne(User::className(), ['id' => 'user_closed']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_created']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_updated']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMtableSessionJoin()
    {
        return $this->hasOne(MtableSessionJoin::className(), ['mtable_session_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoices()
    {
        return $this->hasMany(SaleInvoice::className(), ['mtable_session_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleInvoiceCorrections()
    {
        return $this->hasMany(SaleInvoiceCorrection::className(), ['mtable_session_id' => 'id']);
    }
}
