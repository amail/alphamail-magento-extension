<?php

class Comfirm_AlphaMail_Block_EventLog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('eventLogGrid');
        $this->setDefaultSort('event_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('alphamail/event_log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('event_id', array(
            'header'    => Mage::helper('adminhtml')->__('Id'),
            'width'     => '100px',
            'align'     => 'left',
            'index'     => 'event_id',
        ));
        $this->addColumn('send_id', array(
            'header'    => Mage::helper('adminhtml')->__('Send Id'),
        	'width'     => '100px',
            'align'     => 'left',
            'index'     => 'send_id',
        ));
        $this->addColumn('message', array(
            'header'    => Mage::helper('adminhtml')->__('Message'),
            'index'     => 'message',
        ));
        $this->addColumn('type', array(
            'header'    => Mage::helper('adminhtml')->__('Type'),
            'width'     => '100px',
            'maxwidth'     => '40px',
            'align'     => 'left',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => array(
                0 => 'Information',
                1 => 'Error',
                2 => 'Debug'
            )
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('adminhtml')->__('Created'),
            'width'     => '140px',
            'align'     => 'center',
            'index'     => 'created_at',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('event_id' => $row->getId()));
    }
}

?>
